<?php
namespace VGA\Model;

class Nominee implements \JsonSerializable
{
    const DEFAULT_IMAGE_DIRECTORY = '/img/nominees/';

    /** @var integer */
    private $id;

    /** @var string */
    private $shortName;

    /** @var string */
    private $name;

    /** @var string */
    private $subtitle;

    /** @var string */
    private $image;

    /** @var string */
    private $flavorText;

    /** @var Award */
    private $award;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $shortName
     * @return Nominee
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @param string $name
     * @return Nominee
     */
    public function setName($name)
    {
        $this->name = trim($name);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $subtitle
     * @return Nominee
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = trim($subtitle);
        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param string $image
     * @return Nominee
     */
    public function setImage($image)
    {
        $this->image = trim($image);
        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image ?: (self::DEFAULT_IMAGE_DIRECTORY . $this->getShortName() . '.png');
    }

    /**
     * @param string $flavorText
     * @return Nominee
     */
    public function setFlavorText($flavorText)
    {
        $this->flavorText = trim($flavorText);
        return $this;
    }

    /**
     * @return string
     */
    public function getFlavorText()
    {
        return $this->flavorText;
    }

    /**
     * @param Award $award
     * @return Nominee
     */
    public function setAward(Award $award = null)
    {
        $this->award = $award;
        return $this;
    }

    /**
     * @return Award
     */
    public function getAward()
    {
        return $this->award;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'shortName' => $this->getShortName(),
            'name' => $this->getName(),
            'subtitle' => $this->getSubtitle(),
            'flavorText' => $this->getFlavorText(),
            'image' => $this->getImage()
        ];
    }
}

