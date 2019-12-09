<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_inventory_items")
 * @ORM\Entity
 */
class UserInventoryItem
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
     * @ORM\Column(name="inventory", type="string", length=45)
     */
    private $inventory;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var InventoryItem
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\InventoryItem", inversedBy="userItems")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="itemID", referencedColumnName="id")
     * })
     */
    private $item;

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
     * Set inventory
     *
     * @param string $inventory
     *
     * @return UserInventoryItem
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * Get inventory
     *
     * @return string
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * Set timestamp
     *
     * @param DateTime $timestamp
     *
     * @return UserInventoryItem
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set item
     *
     * @param InventoryItem $item
     *
     * @return UserInventoryItem
     */
    public function setItem(InventoryItem $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return InventoryItem
     */
    public function getItem()
    {
        return $this->item;
    }
}

