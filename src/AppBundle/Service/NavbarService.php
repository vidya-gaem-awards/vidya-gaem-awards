<?php
namespace AppBundle\Service;

use Ehesp\SteamLogin\SteamLogin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\AccessMapInterface;

class NavbarService
{
    private $configService;
    private $router;
    private $accessMap;
    private $authChecker;
    private $tokenStorage;

    public function __construct(ConfigService $configService, RouterInterface $router, AccessMapInterface $accessMap, AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage)
    {
        $this->router = $router;                // Used to get the RouteCollection which we use to fetch the route for each item
        $this->configService = $configService;  // Used to get the list of navbar items, and to determine which pages should be public
        $this->accessMap = $accessMap;          // Used to get role restrictions from security.yml
        $this->authChecker = $authChecker;      // Used to get roles for the current user
        $this->tokenStorage = $tokenStorage;    // Used to check if the user is logged in (authChecker throws exceptions if not)
    }

    public function getItems()
    {
        $navbar = [];
        foreach ($this->configService->getConfig()->getNavbarItems() as $routeName => $title) {
            if ($route = $this->router->getRouteCollection()->get($routeName)) {
                if ($this->canAccessRoute($routeName)) {
                    $navbar[$routeName] = $title;
                }
            } else {
                throw new \Exception('Invalid route \'' . $routeName . '\' . specified in navbar items.');
            }
        }

        return $navbar;
    }

    public function canAccessRoute($routeName)
    {
        $route = $this->router->getRouteCollection()->get($routeName);
        $roles = $this->getRoles($route->getPath());

        // If the list of roles is empty, there are no access restrictions.
        if (empty($roles)) {
            return true;
        }

        // Not logged in
        if (!$this->tokenStorage->getToken()) {
            return false;
        }

        if ($this->authChecker->isGranted($roles, $routeName)) {
            return true;
        }

        return false;
    }

    public function getLoginLink()
    {
        $returnLink = $this->router->generate('login_check', [], UrlGenerator::ABSOLUTE_URL);

        $steam = new SteamLogin();
        return $steam->url($returnLink);
    }

    public function getRoles($path)
    {
        $request = Request::create($path, 'GET');
        list($roles, ) = $this->accessMap->getPatterns($request);
        return $roles ?: [];
    }
}
