<?php
namespace App\Security\EntryPoint;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class SteamEntryPoint implements AuthenticationEntryPointInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $redirect = $this->router->generate(
            'login',
            ['redirect' => $request->getRequestUri()]
        );
        return new RedirectResponse($redirect);
    }
}
