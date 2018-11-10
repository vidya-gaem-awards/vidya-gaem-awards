<?php
namespace App\Controller;

use App\Entity\Action;
use App\Entity\Autocompleter;
use App\Entity\GameRelease;
use App\Entity\TableHistory;
use App\Service\AuditService;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AutocompleterController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $autocompleters = $em->getRepository(Autocompleter::class)->findAll();
        $gameReleases = $em->getRepository( GameRelease::class)->findAll();

        $jsonArray = [];
        foreach ($autocompleters as $autocompleter) {
            $jsonArray[$autocompleter->getId()] = [
                'id' => $autocompleter->getId(),
                'name' => $autocompleter->getName(),
                'suggestions' => $autocompleter->getStrings()
            ];
        }

        return $this->render('autocompleters.html.twig', [
            'autocompleters' => $autocompleters,
            'autocompletersEncodable' => $jsonArray,
            'gameReleases' => count($gameReleases),
        ]);
    }

    public function ajax(EntityManagerInterface $em, Request $request, ConfigService $configService, AuditService $auditService)
    {
        if ($configService->isReadOnly()) {
            return $this->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
        }

        $post = $request->request;

        $id = strtolower($post->get('id'));

        if (strlen($id) === 0) {
            return $this->json(['error' => 'An ID is required.']);
        }

        /** @var Autocompleter $autocompleter */
        $autocompleter = $em->getRepository(Autocompleter::class)->find($id);
        if ($autocompleter && $post->get('action') === 'new') {
            return $this->json(['error' => 'That ID is already in use. Please enter another ID.']);
        } elseif (!$autocompleter && $post->get('action') === 'edit') {
            return $this->json(['error' => 'Couldn\'t find an autocompleter with that ID.']);
        }

        if ($post->get('action') === 'delete') {
            if (!$autocompleter->getAwards()->isEmpty()) {
                return $this->json(['error' => 'Can\'t delete this autocompleter: there are awards still using it (such as the ' . $autocompleter->getAwards()->first()->getName() . ').']);
            }

            $em->remove($autocompleter);
            $em->flush();

            $auditService->add(
                new Action('autocompleter-deleted', $autocompleter->getId())
            );

            return $this->json(['success' => true]);
        } elseif ($post->get('action') === 'new' || $post->get('action') === 'edit') {
            if (!$autocompleter) {
                $autocompleter = new Autocompleter();
                try {
                    $autocompleter->setId($id);
                } catch (\Exception $e) {
                    return $this->json(['error' => 'Invalid autocompleter ID provided.']);
                }
            }

            if (strlen($post->get('name')) === 0) {
                return $this->json(['error' => 'An autocompleter name is required.']);
            }

            $autocompleter
                ->setName($post->get('name'))
                ->setStrings(array_values(array_filter(array_map('trim', explode("\n", $post->get('suggestions'))))));

            $em->persist($autocompleter);
            $em->flush();

            $auditService->add(
                new Action($post->get('action') === 'new' ? 'autocompleter-added' : 'autocompleter-edited', $autocompleter->getId()),
                new TableHistory(Autocompleter::class, $id, $post->all())
            );

            return $this->json(['success' => true]);
        } else {
            return $this->json(['error' => 'Invalid action specified.']);
        }
    }
}
