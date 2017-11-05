<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use RandomLib\Factory;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserListener
{
    private $secret;
    private $tokenStorage;
    private $session;

    public function __construct(string $secret, TokenStorageInterface $tokenStorage, SessionInterface $session)
    {
        $this->secret = $secret;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Generate a random ID to keep in the cookie if one doesn't already exist.
        // We use this cookie as part of the voting identification process.
        $randomIDCookie = $request->cookies->get('access');
        $randomIDSession = $this->session->get('access');

        if ($randomIDCookie && $randomIDSession) {
            $randomID = $randomIDCookie;
        } elseif ($randomIDCookie && !$randomIDSession) {
            $this->session->set('access', $randomIDCookie);
            $randomID = $randomIDCookie;
        } else {
            // Who knows where this came from... it's probably not very secure.
            // Good thing it's not required for anything that's actually important.
            $factory = new Factory;
            $generator = $factory->getLowStrengthGenerator();
            $randomID = hash('sha256', $generator->generate(64));
            $randomID .= ':' . hash_hmac('md5', $randomID, $this->secret);

            $this->session->set('access', $randomID);
        }

        // If the user has a votingCode cookie set, use that, otherwise, use the votingCode session.
        // This helps guard against users with cookies turned off.
        $votingCodeSession = $this->session->get('votingCode');
        $votingCodeCookie = $request->cookies->get('votingCode');

        if ($votingCodeCookie) {
            $this->session->set('votingCode', $votingCodeCookie);
            $votingCode = $votingCodeCookie;
        } else {
            $votingCode = $votingCodeSession;
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $user
            ->setIP($request->server->get('HTTP_CF_CONNECTING_IP', $request->server->get('REMOTE_ADDR')))
            ->setVotingCode($votingCode)
            ->setRandomID($randomID);
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        // If the user didn't have an access cookie when they first loaded the page, one would have been generated
        // in the request handler above. As such, we only need to worry about copying the value from the session
        // into the cookie here.
        $randomIDCookie = $request->cookies->get('access');
        $randomIDSession = $this->session->get('access');

        if ($randomIDSession && !$randomIDCookie) {
            setcookie('access', $randomIDSession, strtotime('+90 days'), '/', $request->getHost());

            $event->getResponse()->headers->setCookie(new Cookie(
                'access',
                $randomIDSession,
                new \DateTime('+90 days'),
                '/',
                $request->getHost()
            ));
        }
    }
}
