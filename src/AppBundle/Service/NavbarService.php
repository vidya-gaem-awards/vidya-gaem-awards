<?php
namespace AppBundle\Service;

use Ehesp\SteamLogin\SteamLogin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\AccessMapInterface;

class NavbarService
{
    private $configService;
    private $navbarItems;
    private $router;
    private $accessMap;
    private $authChecker;
    private $tokenStorage;

    public function __construct($navbarItems, ConfigService $configService, RouterInterface $router, AccessMapInterface $accessMap, AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage)
    {
        $this->navbarItems = $navbarItems;      // Parameter containing the menu items to show
        $this->router = $router;                // Used to get the RouteCollection which we use to fetch the route for each item
        $this->configService = $configService;  // Used to determine which pages should be public
        $this->accessMap = $accessMap;          // Used to get role restrictions from security.yml
        $this->authChecker = $authChecker;      // Used to get roles for the current user
        $this->tokenStorage = $tokenStorage;    // Used to check if the user is logged in (authChecker throws exceptions if not)
    }

    public function getItems()
    {
        $navbar = [];
        foreach ($this->navbarItems as $routeName => $title) {
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

        // We use 'IS_AUTHENTICATED_ANONYMOUSLY' to indicate pages that are hidden at first but become public later.
        // There is almost certainly a better way of doing this.
        if (in_array(AuthenticatedVoter::IS_AUTHENTICATED_ANONYMOUSLY, $roles)) {
            if ($this->configService->getConfig()->isPagePublic($routeName)) {
                return true;
            }

            // Not logged in
            if (!$this->tokenStorage->getToken()) {
                return false;
            }

            if ($this->authChecker->isGranted($route->getDefault('permission'))) {
                return true;
            }

            return false;
        }

        if (empty($roles)) {
            return true;
        }

        // Not logged in
        if (!$this->tokenStorage->getToken()) {
            return false;
        }

        foreach ($roles as $role) {
            if ($this->authChecker->isGranted($role)) {
                return true;
            }
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
