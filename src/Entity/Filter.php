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
     * @ORM\Column(name="id", type="string", length=40)
     * @ORM\Id
     */
    private string $id;

    /**
     * @ORM\Column(name="regex", type="string", length=255, nullable=false)
     */
    private string $regex;

    /**
     * @ORM\Column(name="value", type="integer", nullable=false)
     */
    private int $value;

    /**
     * @var Collection<ResultCache>
     */
    private Collection $resultCache;

    public function __construct()
    {
        $this->resultCache = new ArrayCollection();
    }

    public function setId(string $id): Filter
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setRegex(string $regex): Filter
    {
        $this->regex = $regex;

        return $this;
    }

    public function getRegex(): string
    {
        return $this->regex;
    }

    public function setValue(int $value): Filter
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function addResultCache(ResultCache $resultCache): Filter
    {
        $this->resultCache[] = $resultCache;

        return $this;
    }

    public function removeResultCache(ResultCache $resultCache): void
    {
        $this->resultCache->removeElement($resultCache);
    }

    public function getResultCache(): Collection
    {
        return $this->resultCache;
    }
}

