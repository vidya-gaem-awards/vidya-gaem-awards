<?php

namespace App\Entity;

/**
 * AwardSuggestion
 */
class AwardSuggestion
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
    private $suggestion;

    /**
     * @var Award
     */
    private $award;


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
     * @param User $user
     *
     * @return AwardSuggestion
     */
    public function setUser(User $user)
    {
        $this->user = $user->getFuzzyID();

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
     * Set suggestion
     *
     * @param string $suggestion
     *
     * @return AwardSuggestion
     */
    public function setSuggestion($suggestion)
    {
        $this->suggestion = $suggestion;

        return $this;
    }

    /**
     * Get suggestion
     *
     * @return string
     */
    public function getSuggestion()
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

