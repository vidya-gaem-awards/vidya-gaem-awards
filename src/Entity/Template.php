<?php
namespace App\Entity;

class Template
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $name;

    /*
     * @var string
     */
    private $details;

    /**
     * @var string
     */
    private $source;

    /**
     * @var \DateTime
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
     * @return \DateTime
     */
    public function getLastUpdated(): \DateTime
    {
        return $this->lastUpdated;
    }

    /**
     * @param \DateTime $lastUpdated
     * @return Template
     */
    public function setLastUpdated(\DateTime $lastUpdated): Template
    {
        $this->lastUpdated = $lastUpdated;
        return $this;
    }
}
