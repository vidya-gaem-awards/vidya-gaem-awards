<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'templates')]
#[ORM\Entity]
class Template
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(name: 'filename', type: 'string', length: 100, nullable: false)]
    private string $filename;

    #[ORM\Column(name: 'name', type: 'string', length: 100, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'details', type: 'text', nullable: true)]
    private ?string $details;

    #[ORM\Column(name: 'source', type: 'text', nullable: false)]
    private string $source = '';

    #[ORM\Column(name: 'last_updated', type: 'datetime', nullable: false)]
    private DateTime $lastUpdated;

    public function __construct()
    {
        $this->lastUpdated = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): Template
    {
        $this->filename = $filename;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Template
    {
        $this->name = $name;
        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): Template
    {
        $this->details = $details;
        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): Template
    {
        $this->source = $source;
        return $this;
    }

    public function getLastUpdated(): DateTime
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(DateTime $lastUpdated): Template
    {
        $this->lastUpdated = $lastUpdated;
        return $this;
    }
}
