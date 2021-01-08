<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_inventory_items")
 * @ORM\Entity
 */
class UserInventoryItem
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="user", type="string", length=45, nullable=false)
     */
    private string $user;

    /**
     * @ORM\Column(name="timestamp", type="datetime_immutable", nullable=false)
     */
    private DateTimeImmutable $dateReceived;

    /**
     * @ORM\ManyToOne(targetEntity="LootboxItem", inversedBy="userItems")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="itemID", referencedColumnName="id")
     * })
     */
    private LootboxItem $item;

    public function __construct()
    {
        $this->dateReceived = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setDateReceived(DateTimeImmutable $dateReceived): self
    {
        $this->dateReceived = $dateReceived;

        return $this;
    }

    public function getDateReceived(): DateTimeImmutable
    {
        return $this->dateReceived;
    }

    public function setItem(LootboxItem $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getItem(): LootboxItem
    {
        return $this->item;
    }
}

