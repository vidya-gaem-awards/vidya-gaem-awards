<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Table(name="nominees", options={"collate"="utf8mb4_unicode_ci","charset"="utf8mb4"})
 * @ORM\Entity
 */
class Nominee implements JsonSerializable
{
    const DEFAULT_IMAGE_DIRECTORY = '/img/nominees/';

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
     * @ORM\Column(name="short_name", type="string", length=45, nullable=false)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitle", type="string", length=255, nullable=false)
     */
    private $subtitle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="flavor_text", type="text", nullable=false)
     */
    private $flavorText;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FantasyPrediction", mappedBy="nominee")
     */
    private $fantasyPredictions;

    /**
     * @var Award
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Award", inversedBy="nominees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="awardID", referencedColumnName="id")
     * })
     */
    private $award;

    public function __construct()
    {
        $this->fantasyPredictions = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $shortName
     * @return Nominee
     */
    public function setShortName($shortName): Nominee
    {
        $this->shortName = $shortName;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @param string $name
     * @return Nominee
     */
    public function setName($name): Nominee
    {
        $this->name = trim($name);
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $subtitle
     * @return Nominee
     */
    public function setSubtitle($subtitle): Nominee
    {
        $this->subtitle = trim($subtitle);
        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @param File|null $image
     * @return Nominee
     */
    public function setImage(?File $image): Nominee
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImage(): ?File
    {
        return $this->image;
    }

    /**
     * @param string $flavorText
     * @return Nominee
     */
    public function setFlavorText($flavorText): Nominee
    {
        $this->flavorText = trim($flavorText);
        return $this;
    }

    /**
     * @return string
     */
    public function getFlavorText(): string
    {
        return $this->flavorText;
    }

    /**
     * @param Award $award
     * @return Nominee
     */
    public function setAward(Award $award = null): Nominee
    {
        $this->award = $award;
        return $this;
    }

    /**
     * @return Award
     */
    public function getAward(): Award
    {
        return $this->award;
    }

    /**
     * @return FantasyPrediction[]|ArrayCollection
     */
    public function getFantasyPredictions(): array|ArrayCollection
    {
        return $this->fantasyPredictions;
    }

    public function jsonSerialize(): mixed
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

