<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Table(name="inventory_items")
 * @ORM\Entity
 */
class InventoryItem implements JsonSerializable
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
     * @var int
     *
     * @ORM\Column(name="rarity", type="integer", nullable=false)
     */
    private $rarity;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", nullable=true)
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
     * @var string|null
     *
     * @ORM\Column(name="music_file", type="string", nullable=true)
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
    private $year = '2019';

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
     * @return InventoryItem
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
     * Set rarity
     *
     * @param string $rarity
     *
     * @return InventoryItem
     */
    public function setRarity($rarity)
    {
        $this->rarity = $rarity;

        return $this;
    }

    /**
     * Get rarity
     *
     * @return string
     */
    public function getRarity()
    {
        return $this->rarity;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return InventoryItem
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
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
     * @return InventoryItem
     */
    public function setShortName(string $shortName): InventoryItem
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
     * @return InventoryItem
     */
    public function setCss(bool $css): InventoryItem
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
     * @return InventoryItem
     */
    public function setBuddie(bool $buddie): InventoryItem
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
     * @return InventoryItem
     */
    public function setMusic(bool $music): InventoryItem
    {
        $this->music = $music;
        return $this;
    }

    /**
     * @return string
     */
    public function getMusicFile()
    {
        return $this->musicFile;
    }

    /**
     * @param string $musicFile
     * @return InventoryItem
     */
    public function setMusicFile(string $musicFile): InventoryItem
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
            'rarity' => $this->getRarity(),
            'image' => $this->getImage(),
            'css' => $this->hasCss(),
            'buddie' => $this->isBuddie(),
            'music' => $this->hasMusic(),
            'musicFile' => $this->getMusicFile(),
            'cssContents' => $this->getCssContents(),
            'year' => $this->getYear(),
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
     * @return InventoryItem
     */
    public function setCssContents(?string $cssContents): InventoryItem
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
}
