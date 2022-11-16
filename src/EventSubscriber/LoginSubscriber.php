<?php

namespace App\EventSubscriber;

use App\Entity\Config;
use App\Entity\Login;
use App\Entity\User;
use App\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Knojector\SteamAuthenticationBundle\Event\AuthenticateUserEvent;
use Knojector\SteamAuthenticationBundle\Event\FirstLoginEvent;
use SteamCondenser\Community\SteamId;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoginSubscriber implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Config */
    private $config;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(EntityManagerInterface $em, ConfigService $config, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->config = $config->getConfig();
        $this->dispatcher = $dispatcher;
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticateUserEvent::NAME => 'onAuthenticateUser',
            FirstLoginEvent::NAME => 'onFirstLogin',
        ];
    }

    public function onFirstLogin(FirstLoginEvent $event)
    {
        $communityId = $event->getCommunityId();

        $user = new User();
        $user->setSteamId($communityId);
        $user->setName($communityId);

        $this->dispatcher->dispatch(new AuthenticateUserEvent($user), AuthenticateUserEvent::NAME);
    }

    public function onAuthenticateUser(AuthenticateUserEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();

        $login = new Login();
        $user
            ->addLogin($login)
            ->setLastLogin(new \DateTime());

        if (!$user->getFirstLogin()) {
            $user->setFirstLogin(new \DateTime());
        }

        /** @var SteamId $steam */
        $steam = SteamId::create($user->getSteamId());

        $user->setAvatar($steam->getFullAvatarUrl());
        $user->setName($steam->getNickname());

        $this->em->persist($login);
        $this->em->persist($user);

        if (!$this->config->isReadOnly()) {
            $this->em->flush();
        }
    }
}
