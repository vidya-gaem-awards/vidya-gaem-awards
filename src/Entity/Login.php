<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="logins")
 * @ORM\Entity
 */
class Login
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private DateTime $timestamp;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="logins")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="id", nullable=false)
     * })
     */
    private User $user;

    public function __construct()
    {
        $this->timestamp = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setTimestamp(DateTime $timestamp): Login
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setUser(User $user): Login
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}

