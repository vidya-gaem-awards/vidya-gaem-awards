<?php

namespace AppBundle\Entity;

/**
 * Autocompleter
 */
class Autocompleter
{
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
     * Set id
     *
     * @param string $id
     *
     * @return Autocompleter
     */
    public function setId($id)
    {
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
}

