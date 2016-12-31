<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use VGA\Model\Action;
use VGA\Model\Award;
use VGA\Model\Nominee;
use VGA\Model\TableHistory;

class NomineeController extends BaseController
{
    public function indexAction($awardID = null)
    {
        $repo = $this->em->getRepository(Award::class);
        $query = $repo->createQueryBuilder('c', 'c.id');
        $query->select('c')
            ->where('c.enabled = true')
            ->orderBy('c.order', 'ASC');

        if (!$this->user->canDo('awards-secret')) {
            $query->andWhere('c.secret = false');
        }
        $awards = $query->getQuery()->getResult();

        $awardVariables = [];

        if ($awardID) {
            /** @var Award $award */
            $award = $this->em->getRepository(Award::class)->find($awardID);

            if (!$award || ($award->isSecret() && !$this->user->canDo('awards-secret'))) {
                $this->session->getFlashBag()->add('error', 'Invalid award ID specified.');
                $response = new RedirectResponse($this->generator->generate('nomineeManager'));
                $response->send();
                return;
            }

            $alphabeticalSort = $this->request->get('sort') === 'alphabetical';

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

        $tpl = $this->twig->loadTemplate('nominees.twig');

        $response = new Response($tpl->render(array_merge([
            'title' => 'Nominee Manager',
            'awards' => $awards,
            'award' => $award ?? false,
        ], $awardVariables)));
        $response->send();
    }

    public function postAction($award)
    {
        $response = new JsonResponse();

        if ($this->config->isReadOnly()) {
            $response->setData(['error' => 'The site is currently in read-only mode. No changes can be made.']);
            $response->send();
            return;
        }

        /** @var Award $award */
        $award = $this->em->getRepository(Award::class)->find($award);

        if (!$award || ($award->isSecret() && !$this->user->canDo('awards-secret'))) {
            $response->setData(['error' => 'Invalid award specified.']);
            $response->send();
            return;
        } elseif (!$award->isEnabled()) {
            $response->setData(['error' => 'Award isn\'t enabled.']);
            $response->send();
            return;
        }

        $post = $this->request->request;
        $action = $post->get('action');

        if (!in_array($action, ['new', 'edit', 'delete'], true)) {
            $response->setData(['error' => 'Invalid action specified.']);
            $response->send();
            return;
        }

        if ($action === 'new') {
            if ($award->getNominee($post->get('id'))) {
                $response->setData(['error' => 'A nominee with that ID already exists for this award.']);
                $response->send();
                return;
            } elseif (!$post->get('id')) {
                $response->setData(['error' => 'You need to enter an ID.']);
                $response->send();
                return;
            } elseif (preg_match('/[^a-z0-9-]/', $post->get('id'))) {
                $response->setData(['error' => 'ID can only contain lowercase letters, numbers and dashes.']);
                $response->send();
                return;
            }

            $nominee = new Nominee();
            $nominee
                ->setAward($award)
                ->setShortName($post->get('id'));
        } else {
            $nominee = $award->getNominee($post->get('id'));
            if (!$nominee) {
                $response->setData(['error' => 'Invalid nominee specified.']);
            }
        }

        if ($action === 'delete') {
            $this->em->remove($nominee);

            $action = new Action('nominee-delete');
            $action->setUser($this->user)
                ->setPage(__CLASS__)
                ->setData1($award->getId())
                ->setData2($nominee->getShortName());
            $this->em->persist($action);

            $this->em->flush();

            $response->setData(['success' => true]);
            $response->send();
            return;
        }

        if (strlen(trim($post->get('name', ''))) === 0) {
            $response->setData(['error' => 'You need to enter a name.']);
            $response->send();
            return;
        }

        if (substr($post->get('image', ''), 0, 7) === 'http://') {
            $response->setData(['error' => 'Because this website now uses https, all images now have to start with https:// as well.']);
            $response->send();
            return;
        }

        $nominee
            ->setName($post->get('name'))
            ->setSubtitle($post->get('subtitle'))
            ->setImage($post->get('image'))
            ->setFlavorText($post->get('flavorText'));
        $this->em->persist($nominee);

        $action = new Action('nominee-' . $action);
        $action->setUser($this->user)
            ->setPage(__CLASS__)
            ->setData1($award->getId())
            ->setData2($nominee->getShortName());
        $this->em->persist($action);

        $history = new TableHistory();
        $history->setUser($this->user)
            ->setTable('Nominee')
            ->setEntry($award->getId() . '/' . $nominee->getShortName())
            ->setValues($post->all());
        $this->em->persist($history);
        $this->em->flush();

        $response->setData(['success' => true]);
        $response->send();
    }
}
