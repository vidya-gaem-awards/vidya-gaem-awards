<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'award_suggestions')]
#[ORM\Entity]
class AwardSuggestion
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(name: 'user', type: 'string', length: 45, nullable: false)]
    private string $user;

    #[ORM\Column(name: 'suggestion', type: 'string', nullable: false)]
    private string $suggestion;

    #[ORM\JoinColumn(name: 'awardID', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Award', inversedBy: 'suggestions')]
    private ?Award $award;

    public function getId(): int
    {
        return $this->id;
    }

    public function setUser(BaseUser $user): AwardSuggestion
    {
        $this->user = $user->getFuzzyID();

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setSuggestion(string $suggestion): AwardSuggestion
    {
        $this->suggestion = $suggestion;

        return $this;
    }

    public function getSuggestion(): string
    {
        return $this->suggestion;
    }

    public function setAward(?Award $award = null): AwardSuggestion
    {
        $this->award = $award;

        return $this;
    }

    public function getAward(): ?Award
    {
        return $this->award;
    }
}

