<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="award_feedback")
 * @ORM\Entity
 */
class AwardFeedback
{
    /**
     * @ORM\Column(name="user", type="string", length=45)
     * @ORM\Id
     */
    private string $user;

    /**
     * @ORM\Column(name="opinion", type="smallint", nullable=false)
     */
    private int $opinion;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Award", inversedBy="feedback")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="awardID", referencedColumnName="id")
     * })
     */
    private Award $award;

    public function __construct(Award $award, BaseUser $user)
    {
        $this->award = $award;
        $this->user = $user->getFuzzyID();
    }

    public function setUser(string $user): AwardFeedback
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setOpinion(int $opinion): AwardFeedback
    {
        $this->opinion = $opinion;

        return $this;
    }

    public function getOpinion(): int
    {
        return $this->opinion;
    }

    public function setAward(Award $award): AwardFeedback
    {
        $this->award = $award;

        return $this;
    }

    public function getAward(): Award
    {
        return $this->award;
    }
}

