<?php
namespace AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use VGA\DependencyContainer;

use AppBundle\Entity\Config;
use AppBundle\Entity\User;

abstract class BaseController
{
    /** @var EntityManager */
    protected $em;

    /** @var Request */
    protected $request;

    /** @var \Twig_Environment */
    protected $twig;

    /** @var Session */
    protected $session;

    /** @var User */
    protected $user;

    /** @var UrlGenerator */
    protected $generator;

    /** @var Config */
    protected $config;

    public function __construct(DependencyContainer $container) {
        $this->em = $container->em;
        $this->request = $container->request;
        $this->twig = $container->twig;
        $this->session = $container->session;
        $this->user = $container->user;
        $this->generator = $container->generator;
        $this->config = $container->config;
    }
}
