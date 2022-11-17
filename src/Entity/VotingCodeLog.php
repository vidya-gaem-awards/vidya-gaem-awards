<?php
namespace App\Entity;

use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="voting_code_logs")
 * @ORM\Entity
 */
class VotingCodeLog
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="cookie_id", type="string", length=255, nullable=false)
     */
    private string $cookieID;

    /**
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private DateTime $timestamp;

    /**
     * @ORM\Column(name="ip", type="string", length=45, nullable=false)
     */
    private string $ip;

    /**
     * @ORM\Column(name="code", type="string", length=20, nullable=false)
     */
    private string $code;

    /**
     * @ORM\Column(name="referer", type="string", length=255, nullable=true)
     */
    private ?string $referer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="votes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="id")
     * })
     */
    private ?User $user;

    public function construct(): void
    {
        $this->timestamp = new DateTime();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User|AnonymousUser $user): VotingCodeLog
    {
        if ($user->isLoggedIn()) {
            $this->user = $user;
        }
        $this->ip = $user->getIP();
        $this->cookieID = $user->getRandomID();
        return $this;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTime $timestamp): VotingCodeLog
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): VotingCodeLog
    {
        $this->code = $code;
        return $this;
    }

    public function getReferer(): string
    {
        return $this->referer;
    }

    public function setReferer(string $referer): VotingCodeLog
    {
        $this->referer = mb_substr($referer, 0, 190);
        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): VotingCodeLog
    {
        $this->ip = $ip;
        return $this;
    }

    public function getCookieID(): string
    {
        return $this->cookieID;
    }

    public function setCookieID(string $cookieID): VotingCodeLog
    {
        $this->cookieID = $cookieID;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
