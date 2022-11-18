<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

abstract class BaseUser implements UserInterface
{
    protected string $ipAddress;
    protected string $randomID;
    protected ?string $votingCode = null;

    public function setIP(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getIP(): string
    {
        return $this->ipAddress;
    }

    public function setRandomID(string $randomID): self
    {
        $this->randomID = $randomID;
        return $this;
    }

    public function getRandomID(): string
    {
        return $this->randomID;
    }

    public function getVotingCode(): ?string
    {
        return $this->votingCode;
    }

    public function setVotingCode(?string $votingCode): self
    {
        $this->votingCode = $votingCode;
        return $this;
    }

    abstract public function getFuzzyID(): string;

    public function eraseCredentials() {
        // do nothing
    }
}
