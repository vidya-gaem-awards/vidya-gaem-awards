<?php
namespace App\Entity;

use JsonSerializable;
use RandomLib\Factory;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'advertisements')]
#[ORM\Entity]
class Advertisement implements JsonSerializable
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', nullable: false)]
    private string $name;

    #[ORM\Column(name: 'link', type: 'string', nullable: true)]
    private ?string $link = null;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\File')]
    private ?File $image = null;

    #[ORM\Column(name: 'token', type: 'string', nullable: false)]
    private string $token;

    #[ORM\Column(name: 'special', type: 'boolean', nullable: false)]
    private bool $special;

    #[ORM\Column(name: 'clicks', type: 'integer', nullable: false)]
    private int $clicks = 0;

    public function __construct()
    {
        $factory = new Factory;
        $generator = $factory->getLowStrengthGenerator();
        $this->token = hash('sha1', $generator->generate(64));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): Advertisement
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setLink(?string $link): Advertisement
    {
        $this->link = $link;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setImage(?File $image): Advertisement
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setSpecial(bool $special): Advertisement
    {
        $this->special = $special;

        return $this;
    }

    public function isSpecial(): bool
    {
        return $this->special;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): Advertisement
    {
        $this->token = $token;
        return $this;
    }

    public function getClicks(): int
    {
        return $this->clicks;
    }

    public function setClicks(int $clicks): Advertisement
    {
        $this->clicks = $clicks;
        return $this;
    }

    public function incrementClicks(): static
    {
        $this->clicks++;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'link' => $this->getLink(),
            'image' => $this->getImage(),
            'special' => $this->isSpecial(),
        ];
    }
}
