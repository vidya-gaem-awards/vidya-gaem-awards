<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Action;
use AppBundle\Entity\Autocompleter;
use AppBundle\Entity\Award;
use AppBundle\Entity\TableHistory;

class AwardAdminController extends BaseController
{
    public function managerListAction()
    {
        $repo = $this->em->getRepository(Award::class);
        $query = $repo->createQueryBuilder('a');
        if (!$this->user->canDo('awards-secret')) {
            $query->andWhere('a.secret = false');
        }
        $query->addOrderBy('a.enabled', 'DESC');
        $query->addOrderBy('a.order', 'ASC');
        $query->indexBy('a', 'a.id');
        $awards = $query->getQuery()->getResult();

        if ($this->request->get('sort') === 'percentage') {
            uasort($awards, function (Award $a, Award $b) {
                return $b->getFeedbackPercent()['positive'] <=> $a->getFeedbackPercent()['positive'];
            });
        } elseif ($this->request->get('sort') === 'net') {
            uasort($awards, function (Award $a, Award $b) {
                return $b->getGroupedFeedback()['net'] <=> $a->getGroupedFeedback()['net'];
            });
        }

        $tpl = $this->twig->load('awardManager.twig');

        $autocompleters = $this->em->getRepository(Autocompleter::class)->findAll();

        $variables = [
            'title' => 'Award Manager',
            'awards' => $awards,
            'autocompleters' => $autocompleters
        ];

        $response = new Response($tpl->render($variables));
        $response->send();
    }

    public function managerPostAction()
    {
        $post = $this->request->request;
        $flashbag = $this->session->getFlashBag();

        if ($this->config->isReadOnly()) {
            $flashbag->add('formError', 'The site is currently in read-only mode. No changes can be made.');
            $response = new RedirectResponse($this->generator->generate('awardManager'));
            $response->send();
            return;
        }

        // Open / close all awards
        if ($post->get('action') === 'massChangeNominations') {
            $repo = $this->em->getRepository(Award::class);
            $query = $repo->createQueryBuilder('c');

            if ($post->get('todo') === 'open') {
                $query->update()->set('c.nominationsEnabled', true);
                $query->getQuery()->execute();

                $action = new Action('mass-nomination-change');
                $action->setUser($this->user)
                    ->setPage(__CLASS__)
                    ->setData1('open');
                $this->em->persist($action);
                $flashbag->add('formSuccess', 'Nominations for all awards are now open.');
            } elseif ($post->get('todo') === 'close') {
                $query->update()->set('c.nominationsEnabled', 0);
                $query->getQuery()->execute();

                $action = new Action('mass-nomination-change');
                $action->setUser($this->user)
                    ->setPage(__CLASS__)
                    ->setData1('close');
                $this->em->persist($action);
                $flashbag->add('formSuccess', 'Nominations for all awards are now closed.');
            }

            $this->em->flush();
        }

        $response = new RedirectResponse($this->generator->generate('awardManager'));
        $response->send();
    }

    public function managerPostAjaxAction()
    {
        $response = new JsonResponse();

        if ($this->config->isReadOnly()) {
            $response->setData(['error' => 'The site is currently in read-only mode. No changes can be made.']);
            $response->send();
            return;
        }

        $post = $this->request->request;

        if (strlen($post->get('id')) == 0) {
            $response->setData(['error' => 'An ID is required.']);
            $response->send();
            return;
        }

        $award = $this->em->getRepository(Award::class)->find($post->get('id'));
        if ($award && $post->get('action') === 'new') {
            $response->setData(['error' => 'That ID is already in use. Please enter another ID.']);
            $response->send();
            return;
        } elseif ((!$award || ($award->isSecret() && !$this->user->canDo('awards-secret'))) && $post->get('action') === 'edit') {
            $response->setData(['error' => 'Couldn\'t find an award with that ID.']);
            $response->send();
            return;
        }

        if ($post->get('action') === 'delete') {
            if ($this->user->canDo('awards-delete')) {
                $this->em->remove($award);
                $this->em->flush();

                $action = new Action('award-delete');
                $action->setUser($this->user)
                    ->setPage(__CLASS__)
                    ->setData1($award->getId());
                $this->em->persist($action);
                $this->em->flush();

                $response->setData(['success' => true]);
                $response->send();
            } else {
                $response->setData(['error' => 'You aren\'t allowed to delete awards.']);
                $response->send();
            }
        } elseif ($post->get('action') === 'new' || $post->get('action') === 'edit') {
            if (!$award) {
                $award = new Award();
                try {
                    $award->setId($post->get('id'));
                } catch (\Exception $e) {
                    $response->setData(['error' => 'Invalid award ID provided.']);
                    $response->send();
                    return;
                }
            }

            if (strlen($post->get('name')) === 0) {
                $response->setData(['error' => 'An award name is required.']);
                $response->send();
                return;
            } elseif (strlen($post->get('subtitle')) === 0) {
                $response->setData(['error' => 'A subtitle is required.']);
                $response->send();
                return;
            } elseif (!ctype_digit($post->get('order')) || $post->get('order') > 10000) {
                $response->setData(['error' => 'Position must be between 1 and 10000.']);
                $response->send();
                return;
            }

            if ($post->get('autocompleter')) {
                $autocompleter = $this->em->getRepository(Autocompleter::class)->find($post->get('autocompleter'));
                if (!$autocompleter) {
                    $autocompleter = null;
                }
            } else {
                $autocompleter = null;
            }

            $award
                ->setName($post->get('name'))
                ->setSubtitle($post->get('subtitle'))
                ->setComments($post->get('comments'))
                ->setAutocompleter($autocompleter)
                ->setOrder($post->getInt('order'))
                ->setEnabled((bool)$post->get('enabled'))
                ->setNominationsEnabled((bool)$post->get('nominationsEnabled'))
                ->setSecret((bool)$post->get('secret'));

            $this->em->persist($award);

            $action = new Action($post->get('action') === 'new' ? 'award-added' : 'award-edited');
            $action->setUser($this->user)
                ->setPage(__CLASS__)
                ->setData1($post->get('id'));
            $this->em->persist($action);

            $history = new TableHistory();
            $history->setUser($this->user)
                ->setTable('Award')
                ->setEntry($post->get('id'))
                ->setValues($post->all());
            $this->em->persist($history);
            $this->em->flush();

            $response->setData(['success' => true]);
            $response->send();
        } else {
            $response->setData(['error' => 'Invalid action specified.']);
            $response->send();
        }
    }
}
