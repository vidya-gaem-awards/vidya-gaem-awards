<?php
namespace App\Controller;

use Knojector\SteamAuthenticationBundle\Event\PayloadValidEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use xPaw\Steam\SteamOpenID;

class AuthController extends AbstractController
{
    public function loginAction(RouterInterface $router, Request $request, SessionInterface $session): Response
    {
        $key = $_ENV['STEAM_API_KEY'] ?? false;
        if (!$key) {
            return $this->render('siteConfigIssue.html.twig');
        }

        $session->set('_security.main.target_path', $request->query->get('redirect'));

        $steam = new SteamOpenID($router->generate('loginReturn', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return new RedirectResponse($steam->GetAuthUrl());
    }

    public function loginReturnAction(RouterInterface $router, EventDispatcherInterface $eventDispatcher, SessionInterface $session, UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        $steam = new SteamOpenID($router->generate('loginReturn', [], UrlGeneratorInterface::ABSOLUTE_URL));

        if ($steam->ShouldValidate()) {
            $communityId = $steam->Validate();
        } else {
            return $this->redirectToRoute('loginFailure');
        }

        $eventDispatcher->dispatch(new PayloadValidEvent($communityId), PayloadValidEvent::NAME);

        return new RedirectResponse($session->get('_security.main.target_path') ?: $urlGenerator->generate('home'));
    }

    public function loginFailureAction(): Response
    {
        return $this->render('loginFailure.html.twig');
    }
}
