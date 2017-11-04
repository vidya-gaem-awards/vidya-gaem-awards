<?php
namespace AppBundle\Controller;

use AppBundle\Service\ConfigService;
use AppBundle\Service\NavbarService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\RouterInterface;

class StaticController extends Controller
{
    public function indexAction(RouterInterface $router, ConfigService $configService)
    {
        $defaultPage = $configService->getConfig()->getDefaultPage();
        $defaultRoute = $router->getRouteCollection()->get($defaultPage);

        return $this->forward($defaultRoute->getDefault('_controller'), $defaultRoute->getDefaults());
    }

    public function videosAction(NavbarService $navbar)
    {
        if (!$navbar->canAccessRoute('videos')) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('videos.twig');
    }

    public function soundtrackAction(NavbarService $navbar)
    {
        if (!$navbar->canAccessRoute('soundtrack')) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('soundtrack.twig');
    }

    public function creditsAction(NavbarService $navbar)
    {
        if (!$navbar->canAccessRoute('credits')) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('credits.twig');
    }
}
