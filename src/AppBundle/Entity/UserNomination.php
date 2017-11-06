<?php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class UserNomination
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $nomination;

    /**
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var Award
     */
    private $award;

    /**
     * @param Award $award
     * @param User|UserInterface $user
     * @param string $nomination
     */
    public function __construct(Award $award, User $user, string $nomination)
    {
        $this->award = $award;
        $this->user = $user->getFuzzyID();
        $this->nomination = $nomination;
        $this->timestamp = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return UserNomination
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set nomination
     *
     * @param string $nomination
     *
     * @return UserNomination
     */
    public function setNomination($nomination)
    {
        $this->nomination = $nomination;

        return $this;
    }

    /**
     * Get nomination
     *
     * @return string
     */
    public function getNomination()
    {
        return $this->nomination;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return UserNomination
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set award
     *
     * @param Award $award
     *
     * @return UserNomination
     */
    public function setAward(Award $award = null)
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
}

