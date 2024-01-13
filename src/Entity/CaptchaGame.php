<?php

namespace App\Entity;

use App\Repository\CaptchaGameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaptchaGameRepository::class)]
#[ORM\Table('captcha_games')]
class CaptchaGame implements \JsonSerializable
{
    public const ROWS = ['artificial', 'fake', 'forced', 'cringe', 'honest', 'kino', 'ludo', 'liminal', 'trans', 'goy', 'zoomer', 'boomer', 'weeb', 'based', 'schizo', 'grug'];
    public const COLUMNS = ['soul', 'core', 'pilled', 'kino', 'horny', 'fun', 'cringe', 'reddit', 'slop', 'nostalgia', 'ludo', 'humor', 'shit', 'cute and funny', 'FOTM', 'jank'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $first = null;

    #[ORM\Column(length: 255)]
    private ?string $second = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne]
    private ?File $image = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'image' => $this->getImage(),
            'first' => $this->getFirst(),
            'second' => $this->getSecond(),
        ];
    }
}
