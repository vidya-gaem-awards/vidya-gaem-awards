<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class StaticController extends BaseController
{
    public function privacyAction()
    {
        $response = new Response($this->twig->render('privacy.twig'));
        $response->send();
    }

    public function votingRedirectAction()
    {
        $response = new RedirectResponse($this->generator->generate('detailedResults'));
        $response->send();
    }

    public function videosAction()
    {
        $response = new Response($this->twig->render('videos.twig'));
        $response->send();
    }

    public function soundtrackAction()
    {
        $response = new Response($this->twig->render('soundtrack.twig'));
        $response->send();
    }
}
