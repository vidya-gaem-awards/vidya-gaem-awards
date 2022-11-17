<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="config_key_value")
 * @ORM\Entity()
 */
class ConfigKeyValue
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $value = null;

    public function __construct(string $name)
    {
        $this->setName($name);
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

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
