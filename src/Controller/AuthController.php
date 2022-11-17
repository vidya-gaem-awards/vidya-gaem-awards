<?php
namespace App\Controller;

use Ehesp\SteamLogin\SteamLogin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AuthController extends AbstractController
{
    public function loginAction(RouterInterface $router, Request $request, SessionInterface $session): Response
    {
        $key = $_ENV['STEAM_API_KEY'] ?? false;
        if (!$key) {
            return $this->render('siteConfigIssue.html.twig');
        }

        $session->set('_security.main.target_path', $request->query->get('redirect'));

        $returnLink = $router->generate(
            'steam_authentication_callback',
            [],
            UrlGenerator::ABSOLUTE_URL
        );

        $steam = new SteamLogin();
        return new RedirectResponse($steam->url($returnLink));
    }

    public function loginRedirectAction(SessionInterface $session, UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        return new RedirectResponse($session->get('_security.main.target_path') ?: $urlGenerator->generate('home'));
    }
}
