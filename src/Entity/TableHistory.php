<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'table_history', options: ['collate' => 'utf8mb4_unicode_ci', 'charset' => 'utf8mb4'])]
#[ORM\Entity]
class TableHistory
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(name: '`table`', type: 'string', length: 100, nullable: false)]
    private string $table;

    #[ORM\Column(name: 'entry', type: 'string', length: 255, nullable: false)]
    private string $entry;

    #[ORM\Column(name: '`values`', type: 'json', nullable: false)]
    private array $values;

    #[ORM\Column(name: 'timestamp', type: 'datetime', nullable: false)]
    private DateTime $timestamp;

    #[ORM\JoinColumn(name: 'userID', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: 'App\Entity\User')]
    private User $user;

    public function __construct(string $entityClass, $entityID, array $values)
    {
        $this->setTimestamp(new DateTime());
        $this->table = $entityClass;
        $this->entry = $entityID;
        $this->values = $values;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setTable(string $table): TableHistory
    {
        $this->table = $table;

        return $this;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setEntry(string $entry): TableHistory
    {
        $this->entry = $entry;

        return $this;
    }

    public function getEntry(): string
    {
        return $this->entry;
    }

    public function setValues(array $values): TableHistory
    {
        $this->values = $values;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setTimestamp(DateTime $timestamp): TableHistory
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setUser(User $user): TableHistory
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}

