<?php
namespace AppBundle\Entity;

use RandomLib\Factory;

class Advertisement implements \JsonSerializable
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $link;

    /**
     * @var string
     */
    private $image;

    /**
     * @var string
     */
    private $token;

    /**
     * @var boolean
     */
    private $special;

    /**
     * @return integer
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
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
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Advertisement
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
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
    public function setSpecial($special)
    {
        $this->special = $special;

        return $this;
    }

    /**
     * Get special
     *
     * @return boolean
     */
    public function isSpecial()
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
    public function getClicks()
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
    public function incrementClicks(): Advertisement
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
    public function jsonSerialize()
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
