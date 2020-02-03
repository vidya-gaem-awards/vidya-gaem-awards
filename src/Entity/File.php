<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use RandomLib\Factory;

/**
 * @ORM\Table(name="files")
 * @ORM\Entity
 */
class File implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subdirectory;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $extension;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $entity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubdirectory(): ?string
    {
        return $this->subdirectory;
    }

    public function setSubdirectory(string $subdirectory): self
    {
        $this->subdirectory = $subdirectory;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getURL(): string
    {
        return '/uploads/' . $this->subdirectory . '/' . $this->filename . '.' . $this->extension;
    }

    public function getFullFilename(): string
    {
        return $this->getFilename() . '.' . $this->getExtension();
    }

    /**
     * Returns the path of the file relative to the uploads directory.
     * @return string
     */
    public function getRelativePath(): string
    {
        return $this->getSubdirectory() . '/' . $this->getFullFilename();
    }

    public function jsonSerialize()
    {
        return [
            'fullFilename' => $this->getFullFilename(),
            'relativePath' => $this->getRelativePath(),
            'url' => $this->getURL()
        ];
    }
}
