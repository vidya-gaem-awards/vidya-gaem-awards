<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'votes')]
#[ORM\Entity]
class Vote
{
    #[ORM\Column(name: 'cookie_id', type: 'string', length: 191)]
    #[ORM\Id]
    private string $cookieID;

    #[ORM\Column(name: 'preferences', type: 'json', nullable: false)]
    private array $preferences;

    #[ORM\Column(name: 'timestamp', type: 'datetime', nullable: false)]
    private DateTime $timestamp;

    #[ORM\Column(name: 'ip', type: 'string', length: 45, nullable: false)]
    private string $ip;

    #[ORM\Column(name: 'voting_code', type: 'string', length: 20, nullable: true)]
    private ?string $votingCode;

    #[ORM\Column(name: 'number', type: 'integer', nullable: true)]
    private ?int $number;

    #[ORM\JoinColumn(name: 'awardID', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Award', inversedBy: 'votes')]
    private Award $award;

    #[ORM\JoinColumn(name: 'userID', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: 'App\Entity\User', inversedBy: 'votes')]
    private ?User $user;

    public function setCookieID(string $cookieID): Vote
    {
        $this->cookieID = $cookieID;

        return $this;
    }

    public function getCookieID(): string
    {
        return $this->cookieID;
    }

    public function setPreferences(array $preferences): Vote
    {
        $this->preferences = $preferences;

        return $this;
    }

    public function getPreferences(): array
    {
        return $this->preferences;
    }

    public function setTimestamp(DateTime $timestamp): Vote
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setIp(string $ip): Vote
    {
        $this->ip = $ip;

        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setVotingCode(?string $votingCode): Vote
    {
        $this->votingCode = $votingCode;

        return $this;
    }

    public function getVotingCode(): ?string
    {
        return $this->votingCode;
    }

    public function setNumber(?int $number): Vote
    {
        $this->number = $number;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setAward(Award $award): Vote
    {
        $this->award = $award;

        return $this;
    }

    public function getAward(): Award
    {
        return $this->award;
    }

    public function setUser(?BaseUser $user = null): Vote
    {
        if ($user->isLoggedIn()) {
            $this->user = $user;
        }
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}

