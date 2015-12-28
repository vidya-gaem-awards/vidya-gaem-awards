<?php
namespace VGA;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use VGA\Model\User;

class DependencyContainer
{
    /** @var EntityManager */
    public $em;

    /** @var Request */
    public $request;

    /** @var \PDO */
    public $dbh;

    /** @var \Twig_Environment */
    public $twig;

    /** @var Session */
    public $session;

    /** @var User */
    public $user;

    public function __construct(
        EntityManager $em,
        Request $request,
        \PDO $dbh,
        \Twig_Environment $twig,
        Session $session,
        User $user
    ) {
        $twig->addGlobal('user', $user);
        $twig->addGlobal('flashbag', $session->getFlashBag()->all());

        $this->em = $em;
        $this->request = $request;
        $this->dbh = $dbh;
        $this->twig = $twig;
        $this->session = $session;
        $this->user = $user;
    }
}
