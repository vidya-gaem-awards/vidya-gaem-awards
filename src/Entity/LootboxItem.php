<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Table(name: 'lootbox_items')]
#[ORM\Entity(repositoryClass: 'App\Repository\LootboxItemRepository')]
class LootboxItem implements JsonSerializable, DropChance
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'short_name', type: 'string', length: 50, nullable: false)]
    private string $shortName;

    #[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\File')]
    private ?File $image = null;

    #[ORM\Column(name: 'css', type: 'boolean', nullable: false)]
    private bool $css = false;

    #[ORM\Column(name: 'buddie', type: 'boolean', nullable: false)]
    private bool $buddie = true;

    #[ORM\Column(name: 'music', type: 'boolean', nullable: false)]
    private bool $music = false;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\File')]
    private ?File $musicFile = null;

    #[ORM\Column(name: 'css_contents', type: 'text', nullable: true)]
    private ?string $cssContents = null;

    /**
     * @var Collection<array-key, UserInventoryItem>
     */
    #[ORM\OneToMany(targetEntity: 'App\Entity\UserInventoryItem', mappedBy: 'item')]
    private Collection $userItems;

    #[ORM\Column(type: 'string', length: 10)]
    private string $year = '2023';

    #[ORM\ManyToOne(targetEntity: LootboxTier::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LootboxTier $tier = null;

    #[ORM\Column(name: 'drop_chance', type: 'decimal', precision: 10, scale: 5, nullable: true)]
    private ?string $dropChance = null;

    #[ORM\Column(name: 'absolute_drop_chance', type: 'decimal', precision: 10, scale: 5, nullable: true)]
    private ?string $absoluteDropChance = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 5, nullable: true)]
    private ?string $cachedDropValueStart = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 5, nullable: true)]
    private ?string $cachedDropValueEnd = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $extra = null;

    public function setId($id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): LootboxItem
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
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

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName ): LootboxItem
    {
        $this->shortName = $shortName;
        return $this;
    }

    public function hasCss(): bool
    {
        return $this->css;
    }

    public function setCss( bool $css): LootboxItem
    {
        $this->css = $css;
        return $this;
    }

    public function isBuddie(): bool
    {
        return $this->buddie;
    }

    public function setBuddie( bool $buddie): LootboxItem
    {
        $this->buddie = $buddie;
        return $this;
    }

    public function hasMusic(): bool
    {
        return $this->music;
    }

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

    public function jsonSerialize(): array
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
            'absoluteDropChance' => $this->getAbsoluteDropChance(),
            'extra' => $this->getExtra(),
        ];
    }

    /**
     * @return Collection<array-key, UserInventoryItem>
     */
    public function getUserItems(): Collection
    {
        return $this->userItems;
    }

    public function getCssContents(): ?string
    {
        return $this->cssContents;
    }

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

    public function getExtra(): ?string
    {
        return $this->extra;
    }

    public function setExtra(?string $extra): self
    {
        $this->extra = $extra;

        return $this;
    }
}
