<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="result_cache", options={"collate"="utf8mb4_unicode_ci","charset"="utf8mb4"})
 * @ORM\Entity
 */
class ResultCache
{
    const OFFICIAL_FILTER = '08-4chan-or-null-with-voting-code';

    /**
     * @var string
     *
     * @ORM\Column(name="filter", type="string", length=40)
     * @ORM\Id
     */
    private $filter;

    /**
     * @var array
     *
     * @ORM\Column(name="results", type="json", nullable=false)
     */
    private $results;

    /**
     * @var array
     *
     * @ORM\Column(name="steps", type="json", nullable=false)
     */
    private $steps;

    /**
     * @var array
     *
     * @ORM\Column(name="warnings", type="json", nullable=false)
     */
    private $warnings;

    /**
     * @var int
     *
     * @ORM\Column(name="votes", type="integer", nullable=false)
     */
    private $votes;

    /**
     * @var Award
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Award", inversedBy="resultCache")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="awardID", referencedColumnName="id")
     * })
     */
    private $award;


    /**
     * Set results
     *
     * @param array $results
     *
     * @return ResultCache
     */
    public function setResults($results): ResultCache
    {
        $this->results = $results;

        return $this;
    }

    /**
     * Get results
     *
     * @return array
     */
    public function getResults(): array
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
    public function setSteps($steps): ResultCache
    {
        $this->steps = $steps;

        return $this;
    }

    /**
     * Get steps
     *
     * @return array
     */
    public function getSteps(): array
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
    public function setWarnings($warnings): ResultCache
    {
        $this->warnings = $warnings;

        return $this;
    }

    /**
     * Get warnings
     *
     * @return array
     */
    public function getWarnings(): array
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
    public function setVotes($votes): ResultCache
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Get votes
     *
     * @return integer
     */
    public function getVotes(): int
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
    public function setAward(Award $award): ResultCache
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

    /**
     * Set filter
     *
     * @param string $filter
     *
     * @return ResultCache
     */
    public function setFilter(string $filter): ResultCache
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Get filter
     *
     * @return string
     */
    public function getFilter(): string
    {
        return $this->filter;
    }
}

