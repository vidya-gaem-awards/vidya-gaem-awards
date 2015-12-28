<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;

class StaticController extends BaseController
{
    public function privacyAction()
    {
        $response = new Response($this->twig->render('privacy.twig'));
        $response->send();
    }
}
