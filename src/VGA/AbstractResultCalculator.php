<?php
namespace App\VGA;

use App\Entity\Nominee;

abstract class AbstractResultCalculator
{
    /** @var Nominee[]  */
    protected array $candidates = [];

    protected array $votes = [];

    protected array $warnings = [];

    protected array $steps = [];

    /**
     * @param Nominee[] $candidates
     * @param array $votes
     */
    public function __construct(array $candidates, array $votes)
    {
        $this->candidates = $candidates;
        $this->votes = $votes;
    }

    abstract function calculateResults(): array;

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }
}
