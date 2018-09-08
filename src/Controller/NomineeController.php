<?php
namespace App\Controller;

use App\Service\AuditService;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Action;
use App\Entity\Award;
use App\Entity\Nominee;
use App\Entity\TableHistory;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\VGA\FileSystem;

class NomineeController extends Controller
{
    public function indexAction(?string $awardID, EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker, Request $request)
    {
        $query = $em->createQueryBuilder()
            ->select('a')
            ->from(Award::class, 'a', 'a.id')
            ->where('a.enabled = true')
            ->orderBy('a.order', 'ASC');

        if (!$authChecker->isGranted('ROLE_AWARDS_SECRET')) {
            $query->andWhere('a.secret = false');
        }
        $awards = $query->getQuery()->getResult();

        $awardVariables = [];

        if ($awardID) {
            /** @var Award $award */
            $award = $em->getRepository(Award::class)->find($awardID);

            if (!$award || ($award->isSecret() && !$authChecker->isGranted('ROLE_AWARDS_SECRET'))) {
                $this->addFlash('error', 'Invalid award ID specified.');
                return $this->redirectToRoute('nomineeManager');
            }

            $alphabeticalSort = $request->get('sort') === 'alphabetical';

            $autocompleters = array_filter($award->getUserNominations(), function ($un) {
                return $un['count'] >= 3;
            });
            $autocompleters = array_map(function ($un) {
                return $un['title'];
            }, $autocompleters);
            sort($autocompleters);

            $nomineesArray = [];
            /** @var Nominee $nominee */
            foreach ($award->getNominees() as $nominee) {
                $nomineesArray[$nominee->getShortName()] = $nominee;
            }

            $awardVariables = [
                'alphabeticalSort' => $alphabeticalSort,
                'autocompleters' => $autocompleters,
                'nominees' => $nomineesArray
            ];
        }

        return $this->render('nominees.html.twig', array_merge([
            'title' => 'Nominee Manager',
            'awards' => $awards,
            'award' => $award ?? false,
        ], $awardVariables));
    }

    public function postAction(string $awardID, ConfigService $configService, EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker, Request $request, AuditService $auditService)
    {
        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        /** @var Award $award */
        $award = $em->getRepository(Award::class)->find($awardID);

        if (!$award || ($award->isSecret() && !$authChecker->isGranted('ROLE_AWARDS_SECRET'))) {
            return $this->json(['error' => 'Invalid award specified.']);
        } elseif (!$award->isEnabled()) {
            return $this->json(['error' => 'Award isn\'t enabled.']);
        }

        $post = $request->request;
        $action = $post->get('action');

        if (!in_array($action, ['new', 'edit', 'delete'], true)) {
            return $this->json(['error' => 'Invalid action specified.']);
        }

        if ($action === 'new') {
            if ($award->getNominee($post->get('id'))) {
                return $this->json(['error' => 'A nominee with that ID already exists for this award.']);
            } elseif (!$post->get('id')) {
                return $this->json(['error' => 'You need to enter an ID.']);
            } elseif (preg_match('/[^a-z0-9-]/', $post->get('id'))) {
                return $this->json(['error' => 'ID can only contain lowercase letters, numbers and dashes.']);
            }

            $nominee = new Nominee();
            $nominee
                ->setAward($award)
                ->setShortName($post->get('id'));
        } else {
            $nominee = $award->getNominee($post->get('id'));
            if (!$nominee) {
                $this->json(['error' => 'Invalid nominee specified.']);
            }
        }

        if ($action === 'delete') {
            $em->remove($nominee);
            $auditService->add(
                new Action('nominee-delete', $award->getId(), $nominee->getShortName())
            );
            $em->flush();

            return $this->json(['success' => true]);
        }

        if (strlen(trim($post->get('name', ''))) === 0) {
            return $this->json(['error' => 'You need to enter a name.']);
        }

        if ($request->files->get('image')) {
            try {
                $imagePath = FileSystem::handleUploadedFile(
                    $request->files->get('image'),
                    'nominees',
                    $award->getId() . '--' . $nominee->getShortName()
                );
            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()]);
            }

            $nominee->setImage($imagePath);
        }

        $nominee
            ->setName($post->get('name'))
            ->setSubtitle($post->get('subtitle'))
            ->setFlavorText($post->get('flavorText'));
        $em->persist($nominee);
        $em->flush();

        $auditService->add(
            new Action('nominee-' . $action, $award->getId(), $nominee->getShortName()),
            new TableHistory(Nominee::class, $nominee->getId(), $post->all())
        );

        $em->flush();

        return $this->json(['success' => true]);
    }
}
