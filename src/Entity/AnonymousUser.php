<?php
namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class AnonymousUser implements UserInterface
{
    private string $ipAddress;

    private string $randomID;

    private ?string $votingCode;

    public function setIP(string $ipAddress): AnonymousUser
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getIP(): string
    {
        return $this->ipAddress;
    }

    public function setRandomID(string $randomID): AnonymousUser
    {
        $this->randomID = $randomID;
        return $this;
    }

    public function getRandomID(): string
    {
        return $this->randomID;
    }

    /**
     * A fuzzy ID will be either a user ID (for logged in users) or an IP address (for anonymous users).
     */
    public function getFuzzyID(): string
    {
        return $this->getIP();
    }

    public function getVotingCode(): ?string
    {
        return $this->votingCode;
    }

    public function setVotingCode(?string $votingCode): AnonymousUser
    {
        $this->votingCode = $votingCode;
        return $this;
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

    public function eraseCredentials()
    {
    }
}
