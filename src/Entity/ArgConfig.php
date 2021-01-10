<?php

namespace App\Entity;

use App\Repository\ArgConfigRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArgConfigRepository::class)
 */
class ArgConfig
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $acceptedCodes = [];

    /**
     * @ORM\Column(type="integer")
     */
    private $stage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAcceptedCodes(): ?array
    {
        return $this->acceptedCodes;
    }

    public function setAcceptedCodes(?array $acceptedCodes): self
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
}
