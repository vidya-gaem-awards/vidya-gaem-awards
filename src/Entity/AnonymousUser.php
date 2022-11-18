<?php
namespace App\Entity;

class AnonymousUser extends BaseUser
{
    /**
     * A fuzzy ID will be either a user ID (for logged-in users) or an IP address (for anonymous users).
     */
    public function getFuzzyID(): string
    {
        return $this->getIP();
    }

    public function isLoggedIn(): bool
    {
        return false;
    }

    public function getRoles(): array
    {
        return ['ROLE_ANONYMOUS'];
    }

    public function getUserIdentifier(): string
    {
        return $this->getRandomID();
    }
}
