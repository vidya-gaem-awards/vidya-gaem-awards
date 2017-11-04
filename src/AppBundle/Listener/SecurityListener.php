<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Config;
use AppBundle\Entity\Login;
use AppBundle\Entity\User;
use AppBundle\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Config */
    private $config;

    public function __construct(EntityManagerInterface $em, ConfigService $config)
    {
        $this->em = $em;
        $this->config = $config->getConfig();
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        $login = new Login();
        $user
            ->addLogin($login)
            ->setLastLogin(new \DateTime());
        if (!$user->getFirstLogin()) {
            $user->setFirstLogin(new \DateTime());
        }

        $this->em->persist($login);
        $this->em->persist($user);

        if (!$this->config->isReadOnly()) {
            $this->em->flush();
        }
    }
}
