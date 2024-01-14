<?php

namespace App\Entity;

use App\Repository\RpgCharacterRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RpgCharacterRepository::class)]
#[ORM\Table(name: 'rpg_characters')]
class RpgCharacter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $user;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?DateTimeImmutable $timestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTimestamp(): ?DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTimeImmutable $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
