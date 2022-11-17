<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Table(name="lootbox_tiers")
 * @ORM\Entity(repositoryClass="App\Repository\LootboxTierRepository")
 */
class LootboxTier implements JsonSerializable, DropChance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $color;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5)
     */
    private ?string $drop_chance;

    /**
     * @ORM\OneToMany(targetEntity=LootboxItem::class, mappedBy="tier")
     */
    private ArrayCollection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getDropChance(): ?string
    {
        return $this->drop_chance;
    }

    public function setDropChance(string $drop_chance): self
    {
        $this->drop_chance = $drop_chance;

        return $this;
    }

    /**
     * @return Collection<LootboxItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(LootboxItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setTier($this);
        }

        return $this;
    }

    public function removeItem(LootboxItem $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getTier() === $this) {
                $item->setTier(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'dropChance' => $this->getDropChance(),
            'color' => $this->getColor()
        ];
    }

    public function getAbsoluteDropChance(): ?string {
        return null;
    }
}
