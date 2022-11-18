<?php

namespace App\Security\EntryPoint;

use App\Entity\AnonymousUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly RouterInterface $router,
        private readonly Environment $twig)
    {
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        // Don't automatically redirect for bots (such as the DiscordBot) - show a 401 page instead.
        // The user agent check is intentionally loose, since it doesn't really matter if an actual user gets
        // caught up in it (they just have to click through, which was the normal behaviour prior to 2018).
        $userAgent = $request->headers->get('User-Agent');
        if (stripos($userAgent, 'bot') !== false) {
            return new Response($this->twig->render('bundles/TwigBundle/Exception/error401.html.twig'), 401);
        }

        $user = $this->security->getUser();

        if ($user instanceof AnonymousUser) {
            $redirect = $this->router->generate(
                'login',
                ['redirect' => $request->getRequestUri()]
            );
            return new RedirectResponse($redirect);
        }

        return null;
    }
}
