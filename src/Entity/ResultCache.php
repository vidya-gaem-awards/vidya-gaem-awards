<?php

namespace App\Entity;

/**
 * ResultCache
 */
class ResultCache
{
    const OFFICIAL_FILTER = '08-4chan-or-null-with-voting-code';

    /**
     * @var array
     */
    private $results;

    /**
     * @var array
     */
    private $steps;

    /**
     * @var array
     */
    private $warnings;

    /**
     * @var integer
     */
    private $votes;

    /**
     * @var Award
     */
    private $award;

    /**
     * @var string
     */
    private $filter;


    /**
     * Set results
     *
     * @param array $results
     *
     * @return ResultCache
     */
    public function setResults($results)
    {
        $this->results = $results;

        return $this;
    }

    /**
     * Get results
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Set steps
     *
     * @param array $steps
     *
     * @return ResultCache
     */
    public function setSteps($steps)
    {
        $this->steps = $steps;

        return $this;
    }

    /**
     * Get steps
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * Set warnings
     *
     * @param array $warnings
     *
     * @return ResultCache
     */
    public function setWarnings($warnings)
    {
        $this->warnings = $warnings;

        return $this;
    }

    /**
     * Get warnings
     *
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Set votes
     *
     * @param integer $votes
     *
     * @return ResultCache
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Get votes
     *
     * @return integer
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set award
     *
     * @param Award $award
     *
     * @return ResultCache
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
     * Set filter
     *
     * @param string $filter
     *
     * @return ResultCache
     */
    public function setFilter(string $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Get filter
     *
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }
}

