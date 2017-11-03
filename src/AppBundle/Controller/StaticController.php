<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StaticController extends Controller
{
    public function privacyAction()
    {
        return $this->render('privacy.twig');
    }

    public function votingRedirectAction()
    {
        return $this->redirectToRoute('detailedResults');
    }

    public function videosAction()
    {
        return $this->render('videos.twig');
    }

    public function soundtrackAction()
    {
        return $this->render('soundtrack.twig');
    }
}
