<?php

namespace App\Entity;

use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="news")
 * @ORM\Entity
 */
class News
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
     * @var string|null
     *
     * @ORM\Column(name="headline", type="string", length=255, nullable=true)
     */
    private $headline;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var bool
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible = true;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="id", nullable=false)
     * })
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deletedBy", referencedColumnName="id", nullable=true)
     * })
     */
    private $deletedBy;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set headline
     *
     * @param string $headline
     *
     * @return News
     */
    public function setHeadline($headline): News
    {
        $this->headline = $headline;

        return $this;
    }

    /**
     * Get headline
     *
     * @return string
     */
    public function getHeadline(): string
    {
        return $this->headline;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return News
     */
    public function setText($text): News
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set timestamp
     *
     * @param DateTime $timestamp
     *
     * @return News
     */
    public function setTimestamp($timestamp): News
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return News
     */
    public function setVisible($visible): News
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     *
     * @return News
     */
    public function setUser(UserInterface $user): News
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Returns true if the post was created in the past two days
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->getTimestamp() > new DateTime('-2 days');
    }

    /**
     * Returns true if the post was created more than a week ago
     * @return bool
     */
    public function isOld(): bool
    {
        return $this->getTimestamp() < new DateTime('-7 days');
    }

    /**
     * @return User
     */
    public function getDeletedBy(): User
    {
        return $this->deletedBy;
    }

    /**
     * @param UserInterface $deletedBy
     */
    public function setDeletedBy($deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }
}

