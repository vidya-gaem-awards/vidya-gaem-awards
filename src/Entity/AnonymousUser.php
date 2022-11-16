<?php
namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class AnonymousUser implements UserInterface
{
    /**
     * @var string
     */
    private $ipAddress;

    /**
     * @var string
     */
    private $randomID;

    /**
     * @var string
     */
    private $votingCode;

    /**
     * @param string $ipAddress
     * @return self
     */
    public function setIP($ipAddress): \App\Entity\AnonymousUser
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getIP(): string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $randomID
     * @return self
     */
    public function setRandomID($randomID): \App\Entity\AnonymousUser
    {
        $this->randomID = $randomID;
        return $this;
    }

    /**
     * @return string
     */
    public function getRandomID(): string
    {
        return $this->randomID;
    }

    /**
     * A fuzzy ID will be either a user ID (for logged in users) or an IP address (for anonymous users).
     * @return string
     */
    public function getFuzzyID(): string
    {
        return $this->getIP();
    }

    /**
     * @return mixed
     */
    public function getVotingCode(): mixed
    {
        return $this->votingCode;
    }

    /**
     * @param mixed $votingCode
     * @return self
     */
    public function setVotingCode($votingCode): \App\Entity\AnonymousUser
    {
        $this->votingCode = $votingCode;
        return $this;
    }

    public function isLoggedIn()
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
