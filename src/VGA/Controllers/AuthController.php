<?php
namespace VGA\Controllers;

use Ehesp\SteamLogin\SteamLogin;
use RandomLib;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use VGA\Model\Login;
use VGA\Model\LoginToken;
use VGA\Model\User;

class AuthController extends BaseController
{
    public function loginAction($return)
    {
        $login = new SteamLogin();

        try {
            $steamID = $login->validate();
        } catch (\Exception $e) {
            echo 'Login request timed out or was denied.';
            exit;
        }

        $steam = \SteamId::create($steamID);

        $user = $this->em->getRepository(User::class)->find($steamID);
        if (!$user) {
            $user = new User();
        }

        $login = new Login();

        $avatar = base64_encode(file_get_contents($steam->getIconAvatarUrl()));

        $user
            ->setName($steam->getNickname())
            ->setAvatar($avatar)
            ->addLogin($login)
            ->setLastLogin(new \DateTime());

        $this->em->persist($login);
        $this->em->persist($user);

        // Update the remember me token
        // https://stackoverflow.com/questions/5009685/#5009903
        $factory = new RandomLib\Factory;
        $generator = $factory->getLowStrengthGenerator();
        $randomToken = hash('sha256', $generator->generate(64));
        $randomToken .= ':' . hash_hmac('md5', $randomToken, STEAM_API_KEY);

        $loginToken = $user->getLoginToken();
        if (!$loginToken) {
            $loginToken = new LoginToken();
        }
        $loginToken->setUser($user)
            ->setName($user->getName())
            ->setAvatar($avatar)
            ->setToken($randomToken);

        $this->session->set('user', $steamID);

        $response = new RedirectResponse('https://' . DOMAIN . $return);
        $response->headers->setCookie(new Cookie(
            'rememberMeToken',
            $randomToken,
            new \DateTime('+30 days'),
            '/',
            DOMAIN
        ));
        $response->send();
    }

    public function logoutAction($return)
    {
        $this->session->invalidate();

        // We remove the rememberMeToken, but not the other random cookie.
        $response = new RedirectResponse('https://' . DOMAIN . $return);
        $response->headers->removeCookie('rememberMeToken');
        $response->send();
    }
}
