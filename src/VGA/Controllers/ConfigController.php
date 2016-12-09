<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use VGA\Model\Action;
use VGA\Model\TableHistory;

class ConfigController extends BaseController
{
    public function indexAction()
    {
        $tpl = $this->twig->loadTemplate('config.twig');

        $response = new Response($tpl->render([
            'title' => 'Config',
            'config' => $this->config
        ]));
        $response->send();
    }

    public function postAction()
    {
        $post = $this->request->request;

        $error = false;

        if (!$post->get('votingStart')) {
            $this->config->setVotingStart(null);
        } else {
            try {
                $this->config->setVotingStart(new \DateTime($post->get('votingStart')));
            } catch (\Exception $e) {
                $this->session->getFlashBag()->add('error', 'Invalid date provided for voting start.');
                $error = true;
            }
        }

        if (!$post->get('votingEnd')) {
            $this->config->setVotingEnd(null);
        } else {
            try {
                $this->config->setVotingEnd(new \DateTime($post->get('votingEnd')));
            } catch (\Exception $e) {
                $this->session->getFlashBag()->add('error', 'Invalid date provided for voting end.');
                $error = true;
            }
        }

        if (!$post->get('streamTime')) {
            $this->config->setStreamTime(null);
        } else {
            try {
                $this->config->setStreamTime(new \DateTime($post->get('streamTime')));
            } catch (\Exception $e) {
                $this->session->getFlashBag()->add('error', 'Invalid date provided for stream time.');
                $error = true;
            }
        }

        $this->config->setDefaultPage($post->get('defaultPage'));
        $this->config->setPublicPages(array_keys($post->get('publicPages', [])));

        $this->em->persist($this->config);

        $action = new Action('config-updated');
        $action->setUser($this->user)->setPage(__CLASS__);
        $this->em->persist($action);

        $history = new TableHistory();
        $history->setUser($this->user)->setTable('Config')->setEntry('')->setValues($post->all());
        $this->em->persist($history);

        $this->em->flush();

        if (!$error) {
            $this->session->getFlashBag()->add('success', 'Config successfully saved.');
        }

        $response = new RedirectResponse($this->generator->generate('config'));
        $response->send();
    }
}