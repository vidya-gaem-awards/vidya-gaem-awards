<?php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'user_nominations')]
#[ORM\Entity]
class UserNomination
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(name: 'user', type: 'string', length: 45, nullable: false)]
    private string $user;

    #[ORM\Column(name: 'nomination', type: 'string', length: 255, nullable: false)]
    private string $nomination;

    #[ORM\Column(name: 'timestamp', type: 'datetime', nullable: false)]
    private DateTime $timestamp;

    #[ORM\JoinColumn(name: 'awardID', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Award', inversedBy: 'userNominations')]
    private Award $award;

    public function __construct(Award $award, BaseUser $user, string $nomination)
    {
        $this->award = $award;
        $this->user = $user->getFuzzyID();
        $this->nomination = $nomination;
        $this->timestamp = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setUser(string $user): UserNomination
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setNomination(string $nomination): UserNomination
    {
        $this->nomination = $nomination;

        return $this;
    }

    public function getNomination(): string
    {
        return $this->nomination;
    }

    public function setTimestamp(DateTime $timestamp): UserNomination
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setAward(Award $award = null): UserNomination
    {
        $this->award = $award;

        return $this;
    }

    public function getAward(): Award
    {
        return $this->award;
    }
}

