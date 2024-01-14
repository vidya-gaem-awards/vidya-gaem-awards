<?php

namespace App\Entity;

use App\Repository\CaptchaResponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaptchaResponseRepository::class)]
class CaptchaResponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $user = null;

    #[ORM\Column(length: 255)]
    private ?string $first = null;

    #[ORM\Column(length: 255)]
    private ?string $second = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $timestamp = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $games = [];

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $selected = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getFirst(): ?string
    {
        return $this->first;
    }

    public function setFirst(string $first): static
    {
        $this->first = $first;

        return $this;
    }

    public function getSecond(): ?string
    {
        return $this->second;
    }

    public function setSecond(string $second): static
    {
        $this->second = $second;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getGames(): array
    {
        return $this->games;
    }

    public function setGames(array $games): static
    {
        $this->games = $games;

        return $this;
    }

    public function getSelected(): array
    {
        return $this->selected;
    }

    public function setSelected(array $selected): static
    {
        $this->selected = $selected;

        return $this;
    }
}
