<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'result_cache', options: ['collate' => 'utf8mb4_unicode_ci', 'charset' => 'utf8mb4'])]
#[ORM\Entity]
class ResultCache
{
    const OFFICIAL_FILTER = '08-4chan-or-null-with-voting-code';
    const OFFICIAL_ALGORITHM = 'schulze';

    #[ORM\Column(name: 'filter', type: 'string', length: 40)]
    #[ORM\Id]
    private string $filter;

    #[ORM\Column(name: 'results', type: 'json', nullable: false)]
    private array $results;

    #[ORM\Column(name: 'steps', type: 'json', nullable: false)]
    private array $steps;

    #[ORM\Column(name: 'warnings', type: 'json', nullable: false)]
    private array $warnings;

    #[ORM\Column(name: 'votes', type: 'integer', nullable: false)]
    private int $votes;

    #[ORM\JoinColumn(name: 'awardID', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Award', inversedBy: 'resultCache')]
    private Award $award;

    #[ORM\Column(length: 40)]
    #[ORM\Id]
    private ?string $algorithm = null;


    public function setResults(array $results): ResultCache
    {
        $this->results = $results;

        return $this;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function setSteps(array $steps): ResultCache
    {
        $this->steps = $steps;

        return $this;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function setWarnings(array $warnings): ResultCache
    {
        $this->warnings = $warnings;

        return $this;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function setVotes(int $votes): ResultCache
    {
        $this->votes = $votes;

        return $this;
    }

    public function getVotes(): int
    {
        return $this->votes;
    }

    public function setAward(Award $award): ResultCache
    {
        $this->award = $award;

        return $this;
    }

    public function getAward(): Award
    {
        return $this->award;
    }

    public function setFilter(string $filter): ResultCache
    {
        $this->filter = $filter;

        return $this;
    }

    public function getFilter(): string
    {
        return $this->filter;
    }

    public function getAlgorithm(): ?string
    {
        return $this->algorithm;
    }

    public function setAlgorithm(string $algorithm): static
    {
        $this->algorithm = $algorithm;

        return $this;
    }
}
