<?php
namespace App\VGA;

use App\Entity\Nominee;

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
    abstract function calculateResults(): array;

    /**
     * @return array
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * @return array
     */
    public function getSteps(): array
    {
        return $this->steps;
    }
}
