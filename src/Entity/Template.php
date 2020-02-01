<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="templates")
 * @ORM\Entity
 */
class Template
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
     * @ORM\Column(name="filename", type="string", length=100, nullable=false)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="details", type="text", nullable=true)
     */
    private $details;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="text", nullable=false)
     */
    private $source = '';

    /**
     * @var DateTime
     *
     * @ORM\Column(name="last_updated", type="datetime", nullable=true)
     */
    private $lastUpdated;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return Template
     */
    public function setFilename(string $filename): Template
    {
        $this->filename = $filename;
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
     * @param string $name
     * @return Template
     */
    public function setName(string $name): Template
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }

    /**
     * @param string|null $details
     * @return Template
     */
    public function setDetails(?string $details)
    {
        $this->details = $details;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return Template
     */
    public function setSource(string $source): Template
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLastUpdated(): ?DateTime
    {
        return $this->lastUpdated;
    }

    /**
     * @param DateTime $lastUpdated
     * @return Template
     */
    public function setLastUpdated(DateTime $lastUpdated): Template
    {
        $this->lastUpdated = $lastUpdated;
        return $this;
    }
}
