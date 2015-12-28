<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\Response;

class ErrorController extends BaseController
{
    public function internalErrorAction()
    {
        $response = new Response($this->twig->render('500.twig'), 500);
        $response->send();
        exit;
    }

    public function needLoginAction()
    {
        $response = new Response($this->twig->render('401.twig'), 401);
        $response->send();
        exit;
    }

    public function notFoundAction()
    {
        $response = new Response($this->twig->render('404.twig'), 404);
        $response->send();
        exit;
    }

    public function noAccessAction()
    {
        $response = new Response($this->twig->render('403.twig'), 403);
        $response->send();
        exit;
    }
}
