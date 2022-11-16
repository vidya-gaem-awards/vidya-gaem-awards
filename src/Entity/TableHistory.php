<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="table_history", options={"collate"="utf8mb4_unicode_ci","charset"="utf8mb4"})
 * @ORM\Entity
 */
class TableHistory
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
     * @ORM\Column(name="`table`", type="string", length=100, nullable=false)
     */
    private $table;

    /**
     * @var string
     *
     * @ORM\Column(name="entry", type="string", length=255, nullable=false)
     */
    private $entry;

    /**
     * @var array
     *
     * @ORM\Column(name="`values`", type="json", nullable=false)
     */
    private $values;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="id", nullable=false)
     * })
     */
    private $user;

    public function __construct(string $entityClass, $entityID, array $values)
    {
        $this->setTimestamp(new DateTime());
        $this->table = $entityClass;
        $this->entry = $entityID;
        $this->values = $values;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set table
     *
     * @param string $table
     *
     * @return TableHistory
     */
    public function setTable($table): TableHistory
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get table
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Set entry
     *
     * @param string $entry
     *
     * @return TableHistory
     */
    public function setEntry($entry): TableHistory
    {
        $this->entry = $entry;

        return $this;
    }

    /**
     * Get entry
     *
     * @return string
     */
    public function getEntry(): string
    {
        return $this->entry;
    }

    /**
     * Set values
     *
     * @param array $values
     *
     * @return TableHistory
     */
    public function setValues($values): TableHistory
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Set timestamp
     *
     * @param DateTime $timestamp
     *
     * @return TableHistory
     */
    public function setTimestamp($timestamp): TableHistory
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return TableHistory
     */
    public function setUser(User $user): TableHistory
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}

