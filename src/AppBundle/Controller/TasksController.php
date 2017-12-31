<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Action;
use AppBundle\Entity\Nominee;
use AppBundle\Entity\TableHistory;
use AppBundle\Service\AuditService;
use AppBundle\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use VGA\FileSystem;

class TasksController extends Controller
{
    public function indexAction(EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker)
    {
        $query = $em->createQueryBuilder()
            ->select('n')
            ->from(Nominee::class, 'n')
            ->join('n.award', 'a')
            ->where('a.enabled = 1')
            ->addOrderBy('a.order', 'ASC')
            ->addOrderBy('n.id', 'ASC');

        if (!$authChecker->isGranted('ROLE_AWARDS_SECRET')) {
            $query->andWhere('a.secret = false');
        }

        $flavourText = (clone $query)
            ->andWhere('n.flavorText = \'\'')
            ->getQuery()
            ->getResult();

        $images = (clone $query)
            ->andWhere('n.image IS NULL')
            ->getQuery()
            ->getResult();

        $subtitles = (clone $query)
            ->andWhere('n.subtitle = \'\'')
            ->getQuery()
            ->getResult();

        $totalNominees = $em->createQueryBuilder()
            ->select('COUNT(n)')
            ->from(Nominee::class, 'n')
            ->join('n.award', 'a')
            ->where('a.enabled = true')
            ->getQuery()
            ->getSingleScalarResult();

        $tasks = [
            'Subtitles' => [$subtitles, $totalNominees],
            'Flavor text' => [$flavourText, $totalNominees],
            'Images' => [$images, $totalNominees],
        ];

        $awards = $nominees = [];

        foreach ($tasks as $name => $raw) {
            $data = [
                'id' => str_replace(' ', '-', strtolower($name)),
                'count' => $raw[1] - count($raw[0]),
                'total' => $raw[1]
            ];
            $data['percent'] = $data['count'] / $data['total'] * 100;

            if ($data['percent'] > 90) {
                $data['class'] = 'success';
            } elseif ($data['percent'] > 50) {
                $data['class'] = 'warning';
            } else {
                $data['class'] = 'danger';
            }

            $data['awards'] = [];

            /** @var Nominee $nominee */
            foreach ($raw[0] as $nominee) {
                $award = $nominee->getAward();
                if (!isset($data['awards'][$award->getId()])) {
                    $data['awards'][$award->getId()] = [
                        'award' => $award,
                        'nominees' => [],
                    ];
                }
                $data['awards'][$award->getId()]['nominees'][] = $nominee;

                $awards[$award->getId()] = $award;
                $nominees[$award->getId()][$nominee->getId()] = $nominee;
            }

            $tasks[$name] = $data;
        }

        return $this->render('tasks.html.twig', [
            'title' => 'Tasks',
            'tasks' => $tasks,
            'awards' => $awards,
            'nominees' => $nominees,
        ]);
    }

    public function postAction(ConfigService $configService, AuthorizationCheckerInterface $authChecker, Request $request, EntityManagerInterface $em, AuditService $auditService)
    {
        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $post = $request->request;
        $action = $post->get('action');
        $fullAccess = $authChecker->isGranted('ROLE_NOMINATIONS_EDIT');

        if ($action !== 'nominee') {
            return $this->json(['error' => 'Invalid action specified.']);
        }

        if (!$authChecker->isGranted('ROLE_TASKS_NOMINEES') && !$fullAccess) {
            return $this->json(['error' => 'You don\'t have permission to edit that nominee.']);
        }

        /** @var Nominee $nominee */
        $nominee = $em->getRepository(Nominee::class)->find($post->get('nominee'));

        if (!$nominee || ($nominee->getAward()->isSecret() && !$authChecker->isGranted('ROLE_AWARDS_SECRET'))) {
            return $this->json(['error' => 'Invalid award specified.']);
        } elseif (!$nominee->getAward()->isEnabled()) {
            return $this->json(['error' => 'Award isn\'t enabled.']);
        }

        if ($fullAccess) {
            $nominee->setName($post->get('name'));
        }

        if ($fullAccess || $nominee->getSubtitle() === "") {
            $nominee->setSubtitle($post->get('subtitle'));
        }

        if ($fullAccess || $nominee->getFlavorText() === "") {
            $nominee->setFlavorText($post->get('flavorText'));
        }

        if (($fullAccess || !$nominee->getImage()) && $request->files->get('image')) {
            try {
                $imagePath = FileSystem::handleUploadedFile(
                    $request->files->get('image'),
                    'nominees',
                    $nominee->getAward()->getId() . '--' . $nominee->getShortName()
                );
                $nominee->setImage($imagePath);
                $post->set('image', $imagePath);
            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()]);
            }
        }

        $em->persist($nominee);
        $em->flush();

        $auditService->add(
            new Action('nominee-edit', $nominee->getAward()->getId(), $nominee->getShortName()),
            new TableHistory(Nominee::class, $nominee->getId(), $post->all())
        );

        return $this->json(['success' => true, 'nominee' => $nominee]);
    }
}
