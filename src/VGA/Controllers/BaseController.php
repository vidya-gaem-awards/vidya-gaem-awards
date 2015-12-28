<?php
namespace VGA\Controllers;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use VGA\Model\User;

abstract class BaseController
{
    /** @var EntityManager */
    protected $em;

    /** @var Request */
    protected $request;

    /** @var \PDO */
    protected $dbh;

    /** @var \Twig_Environment */
    protected $twig;

    /** @var Session */
    protected $session;

    /** @var User */
    protected $user;

    public function initialize(
        EntityManager $em,
        Request $request,
        \PDO $dbh,
        \Twig_Environment $twig,
        Session $session,
        User $user
    ) {
        $twig->addGlobal('user', $user);

        $this->em = $em;
        $this->request = $request;
        $this->dbh = $dbh;
        $this->twig = $twig;
        $this->session = $session;
        $this->user = $user;
    }
}
