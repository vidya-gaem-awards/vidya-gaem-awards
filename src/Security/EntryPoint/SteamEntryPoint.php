<?php
namespace App\Security\EntryPoint;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Twig\Environment;

class SteamEntryPoint implements AuthenticationEntryPointInterface
{
    private $router;
    private $twig;

    public function __construct(RouterInterface $router, Environment $twig)
    {
        $this->router = $router;
        $this->twig = $twig;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // Don't automatically redirect for bots (such as the DiscordBot) - show a 401 page instead.
        // The user agent check is intentionally loose, since it doesn't really matter if an actual user gets
        // caught up in it (they just have to click through, which was the normal behaviour prior to 2018).
        $userAgent = $request->headers->get('User-Agent');
        if (stripos($userAgent, 'bot') !== false) {
            return new Response($this->twig->render('bundles/TwigBundle/Exception/error401.html.twig'), 401);
        }

        $redirect = $this->router->generate(
            'login',
            ['redirect' => $request->getRequestUri()]
        );
        return new RedirectResponse($redirect);
    }
}
