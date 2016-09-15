<?php
namespace VGA;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use VGA\Model\Config;
use VGA\Model\User;

class DependencyContainer
{
    /** @var EntityManager */
    public $em;

    /** @var Request */
    public $request;

    /** @var \Twig_Environment */
    public $twig;

    /** @var Session */
    public $session;

    /** @var User */
    public $user;

    /** @var UrlGenerator */
    public $generator;

    /** @var Config */
    public $config;

    public function __construct(
        EntityManager $em,
        Request $request,
        \Twig_Environment $twig,
        Session $session,
        User $user,
        UrlGenerator $generator,
        Config $config
    ) {
        $twig->addGlobal('user', $user);
        $twig->addGlobal('flashbag', $session->getFlashBag());

        $this->em = $em;
        $this->request = $request;
        $this->twig = $twig;
        $this->session = $session;
        $this->user = $user;
        $this->generator = $generator;
        $this->config = $config;
    }
}
