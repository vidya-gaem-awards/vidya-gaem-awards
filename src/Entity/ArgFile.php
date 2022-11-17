<?php

namespace App\Entity;

use App\Repository\ArgFileRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArgFileRepository::class)`
 * @ORM\Table(name="arg_files")
 */
class ArgFile
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
     * @ORM\Column(type="string", length=255)
     */
    private ?string $link;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $thumbnail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $size;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private ?DateTimeImmutable $dateVisible;

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

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getDateVisible(): ?DateTimeImmutable
    {
        return $this->dateVisible;
    }

    public function setDateVisible(?DateTimeImmutable $dateVisible): self
    {
        $this->dateVisible = $dateVisible;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }
}
