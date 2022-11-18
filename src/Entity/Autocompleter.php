<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Table(name="autocompleters", options={"collate"="utf8mb4_unicode_ci","charset"="utf8mb4"})
 * @ORM\Entity
 */
class Autocompleter
{
    const VIDEO_GAMES = 'video-games';

    /**
     * @ORM\Column(name="id", type="string", length=30)
     * @ORM\Id
     */
    private string $id;

    /**
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="strings", type="json", nullable=false)
     */
    private array $strings = [];

    /**
     * @var Collection<Award>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Award", mappedBy="autocompleter")
     */
    private Collection $awards;

    /**
     * @throws Exception
     */
    public function setId(string $id): Autocompleter
    {
        if (!preg_match('/^[A-Za-z0-9-]+$/', $id)) {
            throw new Exception('Invalid ID provided: autocompleter IDs can only consist of numbers, letters, and dashes.');
        }

        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name): Autocompleter
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setStrings(array $strings): Autocompleter
    {
        $this->strings = $strings;

        return $this;
    }

    public function getStrings(): array
    {
        return $this->strings;
    }

    public function addString(string $string): static
    {
        $this->strings[] = $string;
        return $this;
    }

    /**
     * @return Collection<Award>
     */
    public function getAwards(): Collection
    {
        return $this->awards;
    }
}

