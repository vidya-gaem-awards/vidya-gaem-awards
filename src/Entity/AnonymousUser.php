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
    public function setIP($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getIP()
    {
        return $this->ipAddress;
    }

    /**
     * @param string $randomID
     * @return self
     */
    public function setRandomID($randomID)
    {
        $this->randomID = $randomID;
        return $this;
    }

    /**
     * @return string
     */
    public function getRandomID()
    {
        return $this->randomID;
    }

    /**
     * A fuzzy ID will be either a user ID (for logged in users) or an IP address (for anonymous users).
     * @return string
     */
    public function getFuzzyID()
    {
        return $this->getIP();
    }

    /**
     * @return mixed
     */
    public function getVotingCode()
    {
        return $this->votingCode;
    }

    /**
     * @param mixed $votingCode
     * @return self
     */
    public function setVotingCode($votingCode)
    {
        $this->votingCode = $votingCode;
        return $this;
    }

    public function getUsername()
    {
        return 'Anonymous (' . substr($this->getRandomID(), 0, 6) . ')';
    }

    public function isLoggedIn()
    {
        return false;
    }

    public function getRoles(): array
    {
        return ['ROLE_ANONYMOUS'];
    }

    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUserIdentifier()
    {
        return $this->getRandomID();
    }

    public function eraseCredentials()
    {
    }
}
