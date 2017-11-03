<?php
namespace AppBundle\Service;

use Ehesp\SteamLogin\SteamLogin;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class NavbarService
{
    /** @var RouterInterface */
    private $router;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(RouterInterface $router, RequestStack $request)
    {
        $this->router = $router;
        $this->requestStack = $request;
    }

    public function getItems()
    {
        $navbar = [];
        foreach (NAVBAR_ITEMS as $routeName => $title) {
            if ($route = $this->router->getRouteCollection()->get($routeName)) {
                // Only show items in the menu if the user has access to them
                $permission = $route->getDefault('permission');
//                if (!$permission || $user->canDo($permission)) {
                    $navbar[$routeName] = $title;
//                }
            }
        }

        return $navbar;
    }

    public function getLoginLink()
    {
        $returnLink = $this->router->generate(
            'login_check',
            [],
            UrlGenerator::ABSOLUTE_URL
        );

        $steam = new SteamLogin();
        return $steam->url($returnLink);
    }
}
