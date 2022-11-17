<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use JsonSerializable;

/**
 * @ORM\Table(name="awards")
 * @ORM\Entity
 */
class Award implements JsonSerializable
{
    /**
     * @ORM\Column(name="id", type="string", length=30)
     * @ORM\Id
     */
    private string $id;

    /**
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="subtitle", type="string", length=200, nullable=false)
     */
    private string $subtitle;

    /**
     * @ORM\Column(name="`order`",  type="integer", nullable=false)
     */
    private int $order;

    /**
     * @ORM\Column(name="comments", type="text", nullable=true)
     */
    private ?string $comments;

    /**
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private bool $enabled;

    /**
     * @ORM\Column(name="nominations_enabled", type="boolean", nullable=false)
     */
    private bool $nominationsEnabled;

    /**
     * @ORM\Column(name="secret", type="boolean", nullable=false, options={"comment"="Secret awards only show up during voting"})
     */
    private bool $secret;

    /**
     * @var Collection<AwardFeedback>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\AwardFeedback", mappedBy="award", cascade={"remove"})
     */
    private Collection $feedback;

    /**
     * @var Collection<AwardSuggestion>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\AwardSuggestion", mappedBy="award", cascade={"remove"})
     * @ORM\OrderBy({
     *     "suggestion"="ASC"
     * })
     */
    private Collection $suggestions;

    /**
     * @var Collection<Nominee>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Nominee", mappedBy="award", cascade={"remove"})
     * @ORM\OrderBy({
     *     "name"="ASC"
     * })
     */
    private Collection $nominees;

    /**
     * @var Collection<UserNomination>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserNomination", mappedBy="award", cascade={"remove"})
     */
    private Collection $userNominations;

    /**
     * @var Collection<Vote>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="award", cascade={"remove"})
     */
    private Collection $votes;

    /**
     * @var Collection<ResultCache>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ResultCache", mappedBy="award", cascade={"remove"})
     */
    private Collection $resultCache;

    /**
     * @var Collection<FantasyPrediction>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FantasyPrediction", mappedBy="award", cascade={"remove"})
     */
    private Collection $fantasyPredictions;

    /**
     * @var Autocompleter|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Autocompleter", inversedBy="awards")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="autocompleteID", referencedColumnName="id", nullable=true)
     * })
     */
    private ?Autocompleter $autocompleter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     */
    private ?File $winnerImage;

    public function __construct()
    {
        $this->feedback = new ArrayCollection();
        $this->nominees = new ArrayCollection();
        $this->userNominations = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->resultCache = new ArrayCollection();
        $this->suggestions = new ArrayCollection();
        $this->fantasyPredictions = new ArrayCollection();
    }

    /**
     * @throws Exception
     */
    public function setId(string $id): Award
    {
        if (!preg_match('/^[A-Za-z0-9-]+$/', $id)) {
            throw new Exception('Invalid ID provided: award IDs can only consist of numbers, letters, and dashes.');
        }

        $this->id = $id;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name): Award
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setSubtitle(string $subtitle): Award
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setOrder(int $order): Award
    {
        $this->order = $order;

        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setComments(string $comments): Award
    {
        $this->comments = $comments;

        return $this;
    }

    public function getComments(): string
    {
        return $this->comments;
    }

    public function setEnabled(bool $enabled): Award
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setNominationsEnabled(bool $nominationsEnabled): Award
    {
        $this->nominationsEnabled = $nominationsEnabled;

        return $this;
    }

    public function areNominationsEnabled(): bool
    {
        return $this->nominationsEnabled;
    }

    public function setSecret(bool $secret): Award
    {
        $this->secret = $secret;

        return $this;
    }

    public function isSecret(): bool
    {
        return $this->secret;
    }

    public function addFeedback(AwardFeedback $feedback): Award
    {
        $this->feedback[] = $feedback;

        return $this;
    }

    public function removeFeedback(AwardFeedback $feedback)
    {
        $this->feedback->removeElement($feedback);
    }

    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addNominee(Nominee $nominee): Award
    {
        $this->nominees[] = $nominee;

        return $this;
    }

    public function removeNominee(Nominee $nominee)
    {
        $this->nominees->removeElement($nominee);
    }

    public function getNominees(): Collection
    {
        return $this->nominees;
    }

    public function getNominee(string $shortName): ?Nominee
    {
        return $this->getNominees()->filter(function (Nominee $nominee) use ($shortName) {
            return $nominee->getShortName() === $shortName;
        })->first() ?: null;
    }

    public function addUserNomination(UserNomination $userNomination): Award
    {
        $this->userNominations[] = $userNomination;

        return $this;
    }

    public function removeUserNomination(UserNomination $userNomination)
    {
        $this->userNominations->removeElement($userNomination);
    }

    public function getRawUserNominations(): Collection
    {
        return $this->userNominations;
    }

    public function getUserNominations(bool $sortAlphabetically = false): array
    {
        $nominations = [];

        /** @var UserNomination $nomination */
        foreach ($this->userNominations as $nomination) {
            $normalised = trim(strtolower($nomination->getNomination()));
            if (!isset($nominations[$normalised])) {
                $nominations[$normalised] = [
                    'count' => 0,
                    'title' => $nomination->getNomination()
                ];
            }
            $nominations[$normalised]['count']++;
        }

        if ($sortAlphabetically) {
            usort($nominations, function ($a, $b) {
                return strtolower($a['title']) <=> strtolower($b['title']);
            });
        } else {
            usort($nominations, function ($a, $b) {
                if ($b['count'] === $a['count']) {
                    return strtolower($a['title']) <=> strtolower($b['title']);
                }
                return $b['count'] <=> $a['count'];
            });
        }

        return $nominations;
    }

    public function addVote(Vote $vote): Award
    {
        $this->votes[] = $vote;

        return $this;
    }

    public function removeVote(Vote $vote)
    {
        $this->votes->removeElement($vote);
    }

    public function getVotes(): arrayCollection
    {
        return $this->votes;
    }

    public function addResultCache(ResultCache $resultCache): Award
    {
        $this->resultCache[] = $resultCache;

        return $this;
    }

    public function removeResultCache(ResultCache $resultCache)
    {
        $this->resultCache->removeElement($resultCache);
    }

    public function getResultCache(): arrayCollection|array
    {
        return $this->resultCache;
    }

    public function getOfficialResults(): ?ResultCache
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('filter', ResultCache::OFFICIAL_FILTER));

        return $this->getResultCache()->matching($criteria)->first() ?: null;
    }

    public function setAutocompleter(?Autocompleter $autocompleter = null): Award
    {
        $this->autocompleter = $autocompleter;

        return $this;
    }

    public function getAutocompleter(): ?Autocompleter
    {
        return $this->autocompleter;
    }

    public function getGroupedFeedback(): array
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('opinion', 1));
        $positive = count($this->getFeedback()->matching($criteria));

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('opinion', -1));
        $negative = count($this->getFeedback()->matching($criteria));

        return [
            'positive' => $positive,
            'negative' => $negative,
            'net' => $positive - $negative,
            'total' => $positive + $negative
        ];
    }

    public function getFeedbackPercent(): array
    {
        $feedback = $this->getGroupedFeedback();

        if ($feedback['total'] === 0) {
            return [
                'positive' => 0,
                'negative' => 0
            ];
        }

        return [
            'positive' => $feedback['positive'] / $feedback['total'] * 100,
            'negative' => $feedback['negative'] / $feedback['total'] * 100
        ];
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'subtitle' => $this->getSubtitle(),
            'comments' => $this->getComments() ?: '',
            'autocompleter' => $this->getAutocompleter() ? $this->getAutocompleter()->getId() : $this->getId(),
            'order' => $this->getOrder(),
            'enabled' => $this->isEnabled(),
            'nominationsEnabled' => $this->areNominationsEnabled(),
            'secret' => $this->isSecret(),
        ];
    }

    public function addSuggestion(AwardSuggestion $suggestion): Award
    {
        $this->suggestions[] = $suggestion;

        return $this;
    }

    public function removeSuggestion(AwardSuggestion $suggestion)
    {
        $this->suggestions->removeElement($suggestion);
    }

    public function getRawSuggestions(): Collection
    {
        return $this->suggestions;
    }

    public function getNameSuggestions(bool $sortAlphabetically = false): array
    {
        $suggestions = [];

        /** @var AwardSuggestion $suggestion */
        foreach ($this->suggestions as $suggestion) {
            $normalised = trim(strtolower($suggestion->getSuggestion()));
            if (!isset($suggestions[$normalised])) {
                $suggestions[$normalised] = [
                    'count' => 0,
                    'title' => $suggestion->getsuggestion()
                ];
            }
            $suggestions[$normalised]['count']++;
        }

        if ($sortAlphabetically) {
            usort($suggestions, function ($a, $b) {
                return strtolower($a['title']) <=> strtolower($b['title']);
            });
        } else {
            usort($suggestions, function ($a, $b) {
                if ($b['count'] === $a['count']) {
                    return strtolower($a['title']) <=> strtolower($b['title']);
                }
                return $b['count'] <=> $a['count'];
            });
        }

        return $suggestions;
    }

    public function getFantasyPredictions(): Collection
    {
        return $this->fantasyPredictions;
    }

    public function getWinnerImage(): ?File
    {
        return $this->winnerImage;
    }

    public function setWinnerImage(?File $winnerImage): self
    {
        $this->winnerImage = $winnerImage;

        return $this;
    }
}

