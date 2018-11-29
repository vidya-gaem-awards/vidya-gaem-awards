<?php

namespace App\Entity;

class InventoryItem implements \JsonSerializable
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $rarity;

    /**
     * @var string
     */
    private $image;

    /**
     * @var boolean
     */
    private $css = false;

    /**
     * @var boolean
     */
    private $buddie = false;

    /**
     * @var boolean
     */
    private $music = false;

    /**
     * @var string
     */
    private $musicFile;

    /**
     * @var UserInventoryItem[]
     */
    private $userItems;

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
        ];
    }

    /**
     * @return UserInventoryItem[]
     */
    public function getUserItems(): array
    {
        return $this->userItems;
    }
}
