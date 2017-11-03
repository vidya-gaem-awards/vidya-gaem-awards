<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Login;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        $steam = \SteamId::create($user->getSteamID());

        $login = new Login();

        $user
            ->addLogin($login)
            ->setLastLogin(new \DateTime());
        if (!$user->getFirstLogin()) {
            $user->setFirstLogin(new \DateTime());
        }

        $this->em->persist($login);
        $this->em->persist($user);

//        if (!$this->config->isReadOnly()) {
            $this->em->flush();
//        }
    }
}
