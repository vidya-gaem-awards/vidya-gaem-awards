<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use VGA\Model\Action;
use VGA\Model\Autocompleter;
use VGA\Model\Award;
use VGA\Model\TableHistory;
use VGA\Utils;

class AwardAdminController extends BaseController
{
    /**
     * @param Award $awardEditing
     */
    public function managerListAction($awardEditing = null)
    {
        $repo = $this->em->getRepository(Award::class);
        $condition = $this->user->canDo('awards-secret') ? [] : ['secret' => false];
        $awards = $repo->findBy($condition, ['order' => 'ASC']);

        if ($this->request->get('sort') === 'percentage') {
            usort($awards, function (Award $a, Award $b) {
                return $b->getFeedbackPercent()['positive'] <=> $a->getFeedbackPercent()['positive'];
            });
        } elseif ($this->request->get('sort') === 'net') {
            usort($awards, function (Award $a, Award $b) {
                return $b->getGroupedFeedback()['net'] <=> $a->getGroupedFeedback()['net'];
            });
        }

        $tpl = $this->twig->loadTemplate('awardManager.twig');

        $variables = [
            'title' => 'Manage Awards',
            'awards' => $awards
        ];

        if ($awardEditing !== null) {
            $variables['award'] = $awardEditing;
            $variables['editing'] = true;
        }

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

        // Add a new award
        if ($post->get('action') === 'new') {
            if (strlen($post->get('id')) == 0 || strlen($post->get('name')) == 0 ||
                strlen($post->get('subtitle')) == 0 || strlen($post->get('order')) == 0) {
                $flashbag->add('formError', 'You need to fill in all of the fields.');
            } elseif (!preg_match('/^[0-9a-zA-Z-]+$/', $post->get('id'))) {
                $flashbag->add('formError', 'ID can only contain letters, numbers and dashes.');
            } elseif (!ctype_digit($post->get('order'))) {
                $flashbag->add('formError', 'The order must be a positive integer.');
            } elseif (intval($_POST['order']) > 10000) {
                $flashbag->add('formError', 'Order must be less than 10000.');
            } else {
                $award = new Award();
                $award
                    ->setId(strtolower($post->get('id')))
                    ->setName($post->get('name'))
                    ->setSubtitle($post->get('subtitle'))
                    ->setOrder($post->getInt('order'))
                    ->setEnabled($post->getBoolean('enabled'))
                    ->setNominationsEnabled($post->getBoolean('nominations'))
                    ->setSecret($post->getBoolean('secret'));
                $this->em->persist($award);

                $action = new Action('award-added');
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
                $flashbag->add('formSuccess', 'Award successfully added.');
            }
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

    public function editAwardAction($awardID)
    {
        if ($this->config->isReadOnly()) {
            $this->session->getFlashBag()->add('error', 'The site is currently in read-only mode. No changes can be made.');
            $response = new RedirectResponse($this->generator->generate('awardManager'));
            $response->send();
            return;
        }

        /** @var Award $award */
        $award = $this->em->getRepository(Award::class)->find($awardID);

        if (!$award || ($award->isSecret() && !$this->user->canDo('awards-secret'))) {
            $this->session->getFlashBag()->add('error', 'Invalid award ID specified.');
            $response = new RedirectResponse($this->generator->generate('awardManager'));
            $response->send();
            return;
        }

        $autocompleters = $this->em->getRepository(Autocompleter::class)->findAll();
        $this->twig->addGlobal('autocompleters', $autocompleters);

        $this->managerListAction($award);
    }

    public function editAwardPostAction($awardID)
    {
        if ($this->config->isReadOnly()) {
            $this->session->getFlashBag()->add('error', 'The site is currently in read-only mode. No changes can be made.');
            $response = new RedirectResponse($this->generator->generate('awardManager'));
            $response->send();
            return;
        }

        /** @var Award $award */
        $award = $this->em->getRepository(Award::class)->find($awardID);

        if (!$award || ($award->isSecret() && !$this->user->canDo('awards-secret'))) {
            $this->session->getFlashBag()->add('error', 'Invalid award ID specified.');
            $response = new RedirectResponse($this->generator->generate('awardManager'));
            $response->send();
            return;
        }

        $post = $this->request->request;
        $flashbag = $this->session->getFlashBag();

        if ($post->get('delete')) {
            if ($this->user->canDo('awards-delete')) {
                $this->em->remove($award);
                $this->em->flush();

                $flashbag->add('formSuccess', sprintf('Award \'%s\' successfully deleted.', $award->getName()));

                $action = new Action('award-delete');
                $action->setUser($this->user)
                    ->setPage(__CLASS__)
                    ->setData1($award->getId());
                $this->em->persist($action);
                $this->em->flush();
            } else {
                $flashbag->add('formSuccess', 'You aren\'t allowed to delete awards.');
            }
            $response = new RedirectResponse($this->generator->generate('awardManager'));
            $response->send();
        } else {
            if (strlen($post->get('name')) == 0 || strlen($post->get('subtitle')) == 0
                || strlen($post->get('order')) == 0) {
                $flashbag->add('editFormError', 'You need to fill in all of the fields.');
            } elseif (!ctype_digit($post->get('order'))) {
                $flashbag->add('editFormError', 'The order must be a positive integer.');
            } elseif (intval($_POST['order']) > 10000) {
                $flashbag->add('editFormError', 'Order must be less than 10000.');
            } else {
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
                    ->setOrder($post->getInt('order'))
                    ->setAutocompleter($autocompleter)
                    ->setEnabled((bool)$post->get('enabled'))
                    ->setNominationsEnabled((bool)$post->get('nominationsEnabled'))
                    ->setSecret((bool)$post->get('secret'));

                if ($this->user->canDo('voting-results')) {
                    if ($post->get('winnerImage') && !Utils::startsWith($post->get('winnerImage'), 'https://')) {
                        $flashbag->add('editFormError', 'Winner image must start with https://');
                    } else {
                        $award->setWinnerImage($post->get('winnerImage'));
                    }
                }

                $this->em->persist($award);

                $action = new Action('award-edited');
                $action->setUser($this->user)
                    ->setPage(__CLASS__)
                    ->setData1($award->getId());
                $this->em->persist($action);

                $history = new TableHistory();
                $history->setUser($this->user)
                    ->setTable('Award')
                    ->setEntry($award->getId())
                    ->setValues($post->all());
                $this->em->persist($history);
                $this->em->flush();

                $flashbag->add('editFormSuccess', 'Award successfully edited.');
            }

            $response = new RedirectResponse(
                $this->generator->generate('editAward', ['awardID' => $award->getId()])
            );
            $response->send();
        }
    }
}
