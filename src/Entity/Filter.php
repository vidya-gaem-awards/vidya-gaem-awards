<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="filters")
 * @ORM\Entity
 */
class Filter
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=40)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="regex", type="string", length=255, nullable=false)
     */
    private $regex;

    /**
     * @var int
     *
     * @ORM\Column(name="value", type="integer", nullable=false)
     */
    private $value;

    /**
     * @var Collection
     */
    private $resultCache;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->resultCache = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Filter
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
     * Set regex
     *
     * @param string $regex
     *
     * @return Filter
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;

        return $this;
    }

    /**
     * Get regex
     *
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Set value
     *
     * @param integer $value
     *
     * @return Filter
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Add resultCache
     *
     * @param ResultCache $resultCache
     *
     * @return Filter
     */
    public function addResultCache(ResultCache $resultCache)
    {
        $this->resultCache[] = $resultCache;

        return $this;
    }

    /**
     * Remove resultCache
     *
     * @param ResultCache $resultCache
     */
    public function removeResultCache(ResultCache $resultCache)
    {
        $this->resultCache->removeElement($resultCache);
    }

    /**
     * Get resultCache
     *
     * @return Collection
     */
    public function getResultCache()
    {
        return $this->resultCache;
    }
}

