<?php

namespace App\Security;

use App\Entity\AnonymousUser;
use RandomLib\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class AnonymousAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly string $secret,
        private readonly Security $security)
    {
    }

    public function supports(Request $request): ?bool
    {
        // Don't run the authenticator if there's already a user
        $user = $this->security->getUser();
        if ($user) {
            return false;
        }

        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $session = $request->getSession();

        // Generate a random ID to keep in the cookie if one doesn't already exist.
        // We use this cookie as part of the voting identification process.
        $randomIDCookie = $request->cookies->get('access');
        $randomIDSession = $session->get('access');

        if ($randomIDCookie && $randomIDSession) {
            $randomID = $randomIDCookie;
        } elseif ($randomIDCookie && !$randomIDSession) {
            $session->set('access', $randomIDCookie);
            $randomID = $randomIDCookie;
        } else {
            // Who knows where this came from... it's probably not very secure.
            // Good thing it's not required for anything that's actually important.
            $factory = new Factory;
            $generator = $factory->getLowStrengthGenerator();
            $randomID = hash('sha256', $generator->generate(64));
            $randomID .= ':' . hash_hmac('md5', $randomID, $this->secret);

            $session->set('access', $randomID);
        }

        return new SelfValidatingPassport(new UserBadge($randomID, function ($identifier) {
            $user = new AnonymousUser();
            $user->setRandomID($identifier);
            return $user;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}
