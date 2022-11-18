<?php

namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="news")
 * @ORM\Entity
 */
class News
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="headline", type="string", length=255, nullable=true)
     */
    private ?string $headline;

    /**
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private string $text;

    /**
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private DateTime $timestamp;

    /**
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private bool $visible = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="id", nullable=false)
     * })
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deletedBy", referencedColumnName="id", nullable=true)
     * })
     */
    private ?User $deletedBy;


    public function getId(): int
    {
        return $this->id;
    }

    public function setHeadline(string $headline): News
    {
        $this->headline = $headline;

        return $this;
    }

    public function getHeadline(): string
    {
        return $this->headline;
    }

    public function setText(string $text): News
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setTimestamp(DateTime $timestamp): News
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setVisible(bool $visible): News
    {
        $this->visible = $visible;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setUser(User $user): News
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return bool Returns true if the post was created in the past two days
     */
    public function isNew(): bool
    {
        return $this->getTimestamp() > new DateTime('-2 days');
    }

    /**
     * @return bool Returns true if the post was created more than a week ago
     */
    public function isOld(): bool
    {
        return $this->getTimestamp() < new DateTime('-7 days');
    }

    public function getDeletedBy(): ?User
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?User $deletedBy): void
    {
        $this->deletedBy = $deletedBy;
    }
}

