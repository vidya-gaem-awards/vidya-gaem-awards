<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class AwardFeedback
{
    /** @var string */
    private $user;

    /** @var integer */
    private $opinion;

    /** @var Award */
    private $award;

    /**
     * @param Award $award
     * @param User|UserInterface $user
     */
    public function __construct(Award $award, User $user)
    {
        $this->award = $award;
        $this->user = $user->getFuzzyID();
    }

    /**
     * @param string $user
     * @return AwardFeedback
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param integer $opinion
     * @return AwardFeedback
     */
    public function setOpinion($opinion)
    {
        $this->opinion = $opinion;

        return $this;
    }

    /**
     * @return integer
     */
    public function getOpinion()
    {
        return $this->opinion;
    }

    /**
     * @param Award $award
     * @return AwardFeedback
     */
    public function setAward(Award $award)
    {
        $this->award = $award;

        return $this;
    }

    /**
     * @return Award
     */
    public function getAward()
    {
        return $this->award;
    }
}

