<?php
namespace AppBundle\Controller;

use Ehesp\SteamLogin\SteamLogin;
use RandomLib;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\Login;
use AppBundle\Entity\LoginToken;
use AppBundle\Entity\User;

class AuthController extends BaseController
{
    public function loginAction($return)
    {
        $response = new RedirectResponse('https://' . $this->request->getHost() . $return);

        $login = new SteamLogin();

        try {
            $steamID = $login->validate();
        } catch (\Exception $e) {
            $this->session->getFlashBag()->add(
                'error',
                'Login request timed out or was denied.'
            );
            $response->send();
            return;
        }

        $steam = \SteamId::create($steamID);

        $user = $this->em->getRepository(User::class)->find($steamID);
        if (!$user) {
            $user = new User($steamID);
        }

        $login = new Login();

        $avatar = base64_encode(file_get_contents($steam->getMediumAvatarUrl()));

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
        $this->em->persist($loginToken);

        if (!$this->config->isReadOnly()) {
            $this->em->flush();
        }

        $this->session->set('user', $steamID);

        $response->headers->setCookie(new Cookie(
            'rememberMeToken',
            $randomToken,
            new \DateTime('+30 days'),
            '/',
            $this->request->getHost()
        ));
        $response->send();
    }

    public function logoutAction()
    {
        $this->session->invalidate();

        // We remove the rememberMeToken, but not the other randomly generated cookie.
        $response = new RedirectResponse($this->request->server->get('HTTP_REFERER'));
        $response->headers->removeCookie('rememberMeToken');
        $response->send();
    }
}
