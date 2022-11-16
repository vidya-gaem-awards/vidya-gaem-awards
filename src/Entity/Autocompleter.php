<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=30)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="strings", type="json", nullable=false)
     */
    private $strings = [];

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Award", mappedBy="autocompleter")
     */
    private $awards;

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Autocompleter
     * @throws Exception
     */
    public function setId($id): Autocompleter
    {
        if (!preg_match('/^[A-Za-z0-9-]+$/', $id)) {
            throw new Exception('Invalid ID provided: autocompleter IDs can only consist of numbers, letters, and dashes.');
        }

        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Autocompleter
     */
    public function setName($name): Autocompleter
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set strings
     *
     * @param array $strings
     *
     * @return Autocompleter
     */
    public function setStrings($strings): Autocompleter
    {
        $this->strings = $strings;

        return $this;
    }

    /**
     * Get strings
     *
     * @return array
     */
    public function getStrings(): array
    {
        return $this->strings;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function addString(string $string): static
    {
        $this->strings[] = $string;
        return $this;
    }

    /**
     * @return ArrayCollection|Award[]
     */
    public function getAwards(): ArrayCollection|array
    {
        return $this->awards;
    }
}

