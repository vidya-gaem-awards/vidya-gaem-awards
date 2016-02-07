<?php
namespace VGA;

use VGA\Model\Nominee;

abstract class AbstractResultCalculator
{
    /** @var Nominee[]  */
    protected $candidates = [];

    /** @var array */
    protected $votes = [];

    /** @var array */
    protected $warnings = [];

    /** @var array */
    protected $steps = [];

    /**
     * @param Nominee[] $candidates
     * @param array $votes
     */
    public function __construct(array $candidates, array $votes)
    {
        $this->candidates = $candidates;
        $this->votes = $votes;
    }

    /**
     * @return array
     */
    abstract function calculateResults();

    /**
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @return array
     */
    public function getSteps()
    {
        return $this->steps;
    }
}
