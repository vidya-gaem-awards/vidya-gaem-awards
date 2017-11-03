<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * The credits page is not currently dynamic, but it has been in the past.
 * For this reason, it gets its own controller instead of using StaticController.
 */
class CreditsController extends BaseController
{
    public function indexAction()
    {
        $tpl = $this->twig->load('credits.twig');

        $response = new Response($tpl->render([
            'title' => 'Credits',
        ]));
        $response->send();
    }
}
