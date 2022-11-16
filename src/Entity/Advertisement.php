<?php
namespace App\Entity;

use JsonSerializable;
use RandomLib\Factory;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="advertisements")
 * @ORM\Entity
 */
class Advertisement implements JsonSerializable
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
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", nullable=true)
     */
    private $link;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", nullable=false)
     */
    private $token;

    /**
     * @var bool
     *
     * @ORM\Column(name="special", type="boolean", nullable=false)
     */
    private $special;

    /**
     * @var int
     *
     * @ORM\Column(name="clicks", type="integer", nullable=false)
     */
    private $clicks = 0;

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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Advertisement
     */
    public function setName($name): Advertisement
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return Advertisement
     */
    public function setLink($link): Advertisement
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink(): string
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

    /**
     * Set special
     *
     * @param boolean $special
     *
     * @return Advertisement
     */
    public function setSpecial($special): Advertisement
    {
        $this->special = $special;

        return $this;
    }

    /**
     * Get special
     *
     * @return boolean
     */
    public function isSpecial(): bool
    {
        return $this->special;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return Advertisement
     */
    public function setToken(string $token): Advertisement
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClicks(): mixed
    {
        return $this->clicks;
    }

    /**
     * @param int $clicks
     * @return Advertisement
     */
    public function setClicks(int $clicks): Advertisement
    {
        $this->clicks = $clicks;
        return $this;
    }

    /**
     * @return $this
     */
    public function incrementClicks(): static
    {
        $this->clicks++;
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): mixed
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
