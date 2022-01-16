<?php
namespace App\Service;

use App\VGA\NavbarItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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
    private $requestStack;

    public function __construct(ConfigService $configService, RouterInterface $router, AccessMapInterface $accessMap, AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage, RequestStack $requestStack)
    {
        $this->router = $router;                // Used to get the RouteCollection which we use to fetch the route for each item
        $this->configService = $configService;  // Used to get the list of navbar items, and to determine which pages should be public
        $this->accessMap = $accessMap;          // Used to get role restrictions from security.yml
        $this->authChecker = $authChecker;      // Used to get roles for the current user
        $this->tokenStorage = $tokenStorage;    // Used to check if the user is logged in (authChecker throws exceptions if not)
        $this->requestStack = $requestStack;    // Used to show an error to privileged users if the navbar config is broken
    }

    public function getItems()
    {
        /** @var NavbarItem[] $navbar */
        $navbar = [];

        $items = $this->configService->getConfig()->getNavbarItems();
        foreach ($items as $routeName => $details) {
            if (substr($routeName, 0, 8) === 'dropdown') {
                $navbar[$routeName] = new NavbarItem($details);
                continue;
            }

            $title = explode('/', $details['label'], 2);
            if (count($title) === 2) {
                $dropdown = $title[0];
                $title = $title[1];
            } else {
                $dropdown = false;
                $title = $title[0];
            }

            if ($route = $this->router->getRouteCollection()->get($routeName)) {
                if ($this->canAccessRoute($routeName)) {
                    $item = new NavbarItem(['label' => $title, 'order' => $details['order']], $routeName);
                    if ($dropdown) {
                        $navbar[$dropdown]->addChild($item);
                    } else {
                        $navbar[$routeName] = $item;
                    }
                }
            } elseif ($this->authChecker->isGranted('ROLE_EDIT_CONFIG')) {
                $session = $this->requestStack->getSession();
                $session->getFlashBag()->add('error', 'The navigation menu config contains an invalid route (' . $routeName . '). You should fix this ASAP.');
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

        foreach ($roles as $role) {
            if ($this->authChecker->isGranted($role, $routeName)) {
                return true;
            }
        }

        return false;
    }

    public function getRoles($path)
    {
        $request = Request::create($path, 'GET');
        list($roles, ) = $this->accessMap->getPatterns($request);
        return $roles ?: [];
    }
}
