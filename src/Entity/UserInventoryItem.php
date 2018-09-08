<?php

namespace App\Entity;

/**
 * UserInventoryItem
 */
class UserInventoryItem
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $inventory;

    /**
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var InventoryItem
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
     * @param \DateTime $timestamp
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
     * @return \DateTime
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

