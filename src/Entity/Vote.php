<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="votes")
 * @ORM\Entity
 */
class Vote
{
    /**
     * @var string
     *
     * @ORM\Column(name="cookie_id", type="string", length=191)
     * @ORM\Id
     */
    private $cookieID;

    /**
     * @var array
     *
     * @ORM\Column(name="preferences", type="json_array", nullable=false)
     */
    private $preferences;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=45, nullable=false)
     */
    private $ip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="voting_code", type="string", length=20, nullable=true)
     */
    private $votingCode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="number", type="integer", nullable=true)
     */
    private $number;

    /**
     * @var Award
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Award", inversedBy="votes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="awardID", referencedColumnName="id")
     * })
     */
    private $award;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="votes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * Set cookieID
     *
     * @param string $cookieID
     *
     * @return Vote
     */
    public function setCookieID($cookieID)
    {
        $this->cookieID = $cookieID;

        return $this;
    }

    /**
     * Get cookieID
     *
     * @return string
     */
    public function getCookieID()
    {
        return $this->cookieID;
    }

    /**
     * Set preferences
     *
     * @param array $preferences
     *
     * @return Vote
     */
    public function setPreferences($preferences)
    {
        $this->preferences = $preferences;

        return $this;
    }

    /**
     * Get preferences
     *
     * @return array
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * Set timestamp
     *
     * @param DateTime $timestamp
     *
     * @return Vote
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Vote
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set votingCode
     *
     * @param string $votingCode
     *
     * @return Vote
     */
    public function setVotingCode($votingCode)
    {
        $this->votingCode = $votingCode;

        return $this;
    }

    /**
     * Get votingCode
     *
     * @return string
     */
    public function getVotingCode()
    {
        return $this->votingCode;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Vote
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set award
     *
     * @param Award $award
     *
     * @return Vote
     */
    public function setAward(Award $award)
    {
        $this->award = $award;

        return $this;
    }

    /**
     * Get award
     *
     * @return Award
     */
    public function getAward()
    {
        return $this->award;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     *
     * @return Vote
     */
    public function setUser(User $user = null)
    {
        if ($user->isLoggedIn()) {
            $this->user = $user;
        }
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}

