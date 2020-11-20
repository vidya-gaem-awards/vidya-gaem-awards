<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Table(name="lootbox_items")
 * @ORM\Entity(repositoryClass="App\Repository\LootboxItemRepository")
 */
class LootboxItem implements JsonSerializable, DropChance
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=50, nullable=false)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     */
    private $image;

    /**
     * @var bool
     *
     * @ORM\Column(name="css", type="boolean", nullable=false)
     */
    private $css = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="buddie", type="boolean", nullable=false)
     */
    private $buddie = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="music", type="boolean", nullable=false)
     */
    private $music = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     */
    private $musicFile;

    /**
     * @var string|null
     *
     * @ORM\Column(name="css_contents", type="text", nullable=true)
     */
    private $cssContents;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserInventoryItem", mappedBy="item")
     */
    private $userItems;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $year = '2020';

    /**
     * @ORM\ManyToOne(targetEntity=LootboxTier::class, inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tier;

    /**
     * @ORM\Column(name="drop_chance", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $dropChance;

    /**
     * @ORM\Column(name="absolute_drop_chance", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $absoluteDropChance;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5, nullable=true)
     */
    private $cachedDropValueStart;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5, nullable=true)
     */
    private $cachedDropValueEnd;

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
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
     * @return LootboxItem
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

    public function setImage(?File $image): LootboxItem
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @param string $shortName
     *
     * @return LootboxItem
     */
    public function setShortName(string $shortName ): LootboxItem
    {
        $this->shortName = $shortName;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasCss(): bool
    {
        return $this->css;
    }

    /**
     * @param bool $css
     *
     * @return LootboxItem
     */
    public function setCss( bool $css): LootboxItem
    {
        $this->css = $css;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBuddie(): bool
    {
        return $this->buddie;
    }

    /**
     * @param bool $buddie
     *
     * @return LootboxItem
     */
    public function setBuddie( bool $buddie): LootboxItem
    {
        $this->buddie = $buddie;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasMusic(): bool
    {
        return $this->music;
    }

    /**
     * @param bool $music
     *
     * @return LootboxItem
     */
    public function setMusic(bool $music): LootboxItem
    {
        $this->music = $music;
        return $this;
    }

    public function getMusicFile(): ?File
    {
        return $this->musicFile;
    }

    public function setMusicFile( ?File $musicFile): LootboxItem
    {
        $this->musicFile = $musicFile;
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'shortName' => $this->getShortName(),
            'name' => $this->getName(),
            'image' => $this->getImage(),
            'css' => $this->hasCss(),
            'buddie' => $this->isBuddie(),
            'music' => $this->hasMusic(),
            'musicFile' => $this->getMusicFile(),
            'cssContents' => $this->getCssContents(),
            'year' => $this->getYear(),
            'tier' => $this->getTier()->getId(),
            'dropChance' => $this->getDropChance(),
            'absoluteDropChance' => $this->getAbsoluteDropChance()
        ];
    }

    /**
     * @return UserInventoryItem[]
     */
    public function getUserItems(): array
    {
        return $this->userItems;
    }

    /**
     * @return string|null
     */
    public function getCssContents(): ?string
    {
        return $this->cssContents;
    }

    /**
     * @param string|null $cssContents
     *
     * @return LootboxItem
     */
    public function setCssContents(?string $cssContents): LootboxItem
    {
        $this->cssContents = $cssContents;
        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getTier(): ?LootboxTier
    {
        return $this->tier;
    }

    public function setTier(?LootboxTier $tier): self
    {
        $this->tier = $tier;

        return $this;
    }

    public function getDropChance(): ?string
    {
        return $this->dropChance;
    }

    public function setDropChance(?string $dropChance): self
    {
        $this->dropChance = $dropChance;

        return $this;
    }

    public function getAbsoluteDropChance(): ?string
    {
        return $this->absoluteDropChance;
    }

    public function setAbsoluteDropChance(?string $absoluteDropChance): self
    {
        $this->absoluteDropChance = $absoluteDropChance;

        return $this;
    }

    public function getCachedDropValueStart(): ?string
    {
        return $this->cachedDropValueStart;
    }

    public function setCachedDropValueStart(?string $cachedDropValueStart): self
    {
        $this->cachedDropValueStart = $cachedDropValueStart;

        return $this;
    }

    public function getCachedDropValueEnd(): ?string
    {
        return $this->cachedDropValueEnd;
    }

    public function setCachedDropValueEnd(?string $cachedDropValueEnd): self
    {
        $this->cachedDropValueEnd = $cachedDropValueEnd;

        return $this;
    }
}
