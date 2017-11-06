<?php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class VotingCodeLog
{
    /** @var integer */
    private $id;

    /** @var User */
    private $user;

    /** @var \DateTime */
    private $timestamp;

    /** @var string */
    private $code;

    /** @var string */
    private $referer;

    /** @var string */
    private $ip;

    /** @var string */
    private $cookieID;

    public function construct()
    {
        $this->timestamp = new \DateTime();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User|UserInterface $user
     * @return VotingCodeLog
     */
    public function setUser($user)
    {
        if ($user->isLoggedIn()) {
            $this->user = $user;
        }
        $this->ip = $user->getIP();
        $this->cookieID = $user->getRandomID();
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     * @return VotingCodeLog
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return VotingCodeLog
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * @param string $referer
     * @return VotingCodeLog
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return VotingCodeLog
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return string
     */
    public function getCookieID()
    {
        return $this->cookieID;
    }

    /**
     * @param string $cookieID
     * @return VotingCodeLog
     */
    public function setCookieID($cookieID)
    {
        $this->cookieID = $cookieID;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
