<?php

namespace VGA\Model;

/**
 * Filter
 */
class Filter
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $regex;

    /**
     * @var integer
     */
    private $value;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $resultCache;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->resultCache = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \VGA\Model\ResultCache $resultCache
     *
     * @return Filter
     */
    public function addResultCache(\VGA\Model\ResultCache $resultCache)
    {
        $this->resultCache[] = $resultCache;

        return $this;
    }

    /**
     * Remove resultCache
     *
     * @param \VGA\Model\ResultCache $resultCache
     */
    public function removeResultCache(\VGA\Model\ResultCache $resultCache)
    {
        $this->resultCache->removeElement($resultCache);
    }

    /**
     * Get resultCache
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResultCache()
    {
        return $this->resultCache;
    }
}

