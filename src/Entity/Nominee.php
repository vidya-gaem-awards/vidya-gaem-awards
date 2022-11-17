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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="short_name", type="string", length=45, nullable=false)
     */
    private string $shortName;

    /**
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="subtitle", type="string", length=255, nullable=false)
     */
    private string $subtitle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     */
    private ?File $image;

    /**
     * @ORM\Column(name="flavor_text", type="text", nullable=false)
     */
    private string $flavorText;

    /**
     * @var Collection<FantasyPrediction>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FantasyPrediction", mappedBy="nominee")
     */
    private Collection $fantasyPredictions;

    /**
     * @var Award
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Award", inversedBy="nominees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="awardID", referencedColumnName="id")
     * })
     */
    private Award $award;

    public function __construct()
    {
        $this->fantasyPredictions = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setShortName(string $shortName): Nominee
    {
        $this->shortName = $shortName;
        return $this;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setName(string $name): Nominee
    {
        $this->name = trim($name);
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setSubtitle(string $subtitle): Nominee
    {
        $this->subtitle = trim($subtitle);
        return $this;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setImage(?File $image): Nominee
    {
        $this->image = $image;
        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setFlavorText(string $flavorText): Nominee
    {
        $this->flavorText = trim($flavorText);
        return $this;
    }

    public function getFlavorText(): string
    {
        return $this->flavorText;
    }

    public function setAward(Award $award = null): Nominee
    {
        $this->award = $award;
        return $this;
    }

    public function getAward(): Award
    {
        return $this->award;
    }

    /**
     * @return Collection<FantasyPrediction>
     */
    public function getFantasyPredictions(): Collection
    {
        return $this->fantasyPredictions;
    }

    public function jsonSerialize(): array
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

