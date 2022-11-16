<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="award_suggestions")
 * @ORM\Entity
 */
class AwardSuggestion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=45, nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="suggestion", type="string", nullable=false)
     */
    private $suggestion;

    /**
     * @var Award
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Award", inversedBy="suggestions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="awardID", referencedColumnName="id", nullable=true)
     * })
     */
    private $award;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return AwardSuggestion
     */
    public function setUser(User $user): AwardSuggestion
    {
        $this->user = $user->getFuzzyID();

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * Set suggestion
     *
     * @param string $suggestion
     *
     * @return AwardSuggestion
     */
    public function setSuggestion($suggestion): AwardSuggestion
    {
        $this->suggestion = $suggestion;

        return $this;
    }

    /**
     * Get suggestion
     *
     * @return string
     */
    public function getSuggestion(): string
    {
        return $this->suggestion;
    }

    /**
     * Set award
     *
     * @param Award $award
     *
     * @return AwardSuggestion
     */
    public function setAward(Award $award = null): AwardSuggestion
    {
        $this->award = $award;

        return $this;
    }

    /**
     * Get award
     *
     * @return Award
     */
    public function getAward(): Award
    {
        return $this->award;
    }
}

