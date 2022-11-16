<?php

namespace App\Security;

use App\Entity\AnonymousUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AnonymousUserProvider implements UserProviderInterface
{
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass(string $class)
    {
        return $class === AnonymousUser::class;
    }

    public function loadUserByUsername(string $username)
    {
    }

    public function loadUserByIdentifier(string $identifier)
    {
    }
}
