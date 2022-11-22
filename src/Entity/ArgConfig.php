<?php

namespace App\Entity;

use App\Repository\ArgConfigRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ArgConfig
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="json")
     */
    private array $acceptedCodes = [];

    /**
     * @ORM\Column(type="integer")
     */
    private int $stage = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $codeCount = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $finished = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $nextThreshold;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getAcceptedCodes(): ?array
    {
        return $this->acceptedCodes;
    }

    public function setAcceptedCodes(array $acceptedCodes): self
    {
        $this->acceptedCodes = $acceptedCodes;

        return $this;
    }

    public function getStage(): ?int
    {
        return $this->stage;
    }

    public function setStage(int $stage): self
    {
        $this->stage = $stage;

        return $this;
    }

    public function getCodeCount(): ?int
    {
        return $this->codeCount;
    }

    public function setCodeCount(?int $codeCount): self
    {
        $this->codeCount = $codeCount;

        return $this;
    }

    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }

    public function getNextThreshold(): ?int
    {
        return $this->nextThreshold;
    }

    public function setNextThreshold(?int $nextThreshold): self
    {
        $this->nextThreshold = $nextThreshold;

        return $this;
    }
}
