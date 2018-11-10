<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Autocompleter
 */
class Autocompleter
{
    const VIDEO_GAMES = 'video-games';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $strings = [];

    /**
     * @var ArrayCollection|Award[]
     */
    private $awards;

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Autocompleter
     * @throws \Exception
     */
    public function setId($id)
    {
        if (!preg_match('/^[A-Za-z0-9-]+$/', $id)) {
            throw new \Exception('Invalid ID provided: autocompleter IDs can only consist of numbers, letters, and dashes.');
        }

        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
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
    public function setStrings($strings)
    {
        $this->strings = $strings;

        return $this;
    }

    /**
     * Get strings
     *
     * @return array
     */
    public function getStrings()
    {
        return $this->strings;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function addString(string $string)
    {
        $this->strings[] = $string;
        return $this;
    }

    /**
     * @return ArrayCollection|Award[]
     */
    public function getAwards()
    {
        return $this->awards;
    }
}

