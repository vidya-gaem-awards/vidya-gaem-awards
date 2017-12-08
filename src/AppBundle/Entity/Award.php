<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Award
 */
class Award implements \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $subtitle;

    /**
     * @var integer
     */
    private $order;

    /**
     * @var string
     */
    private $comments;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var boolean
     */
    private $nominationsEnabled;

    /**
     * @var boolean
     */
    private $secret;

    /**
     * @var arrayCollection
     */
    private $feedback;

    /**
     * @var arrayCollection
     */
    private $nominees;

    /**
     * @var arrayCollection
     */
    private $userNominations;

    /**
     * @var arrayCollection
     */
    private $votes;

    /**
     * @var arrayCollection
     */
    private $resultCache;

    /**
     * @var Autocompleter
     */
    private $autocompleter;

    /** @var string */
    private $winnerImage;
    
    /** @var AwardSuggestion */
    private $suggestions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->feedback = new ArrayCollection();
        $this->nominees = new ArrayCollection();
        $this->userNominations = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->resultCache = new ArrayCollection();
        $this->suggestions = new ArrayCollection();
    }

    /**
     * @param string $id
     * @return Award
     * @throws \Exception
     */
    public function setId($id)
    {
        if (!preg_match('/^[A-Za-z0-9-]+$/', $id)) {
            throw new \Exception('Invalid ID provided: award IDs can only consist of numbers, letters, and dashes.');
        }

        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Award
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
     * Set subtitle
     *
     * @param string $subtitle
     *
     * @return Award
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return Award
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set comments
     *
     * @param string $comments
     *
     * @return Award
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Award
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set nominationsEnabled
     *
     * @param boolean $nominationsEnabled
     *
     * @return Award
     */
    public function setNominationsEnabled($nominationsEnabled)
    {
        $this->nominationsEnabled = $nominationsEnabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function areNominationsEnabled()
    {
        return $this->nominationsEnabled;
    }

    /**
     * Set secret
     *
     * @param boolean $secret
     *
     * @return Award
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get secret
     *
     * @return boolean
     */
    public function isSecret()
    {
        return $this->secret;
    }

    /**
     * Add feedback
     *
     * @param AwardFeedback $feedback
     *
     * @return Award
     */
    public function addFeedback(AwardFeedback $feedback)
    {
        $this->feedback[] = $feedback;

        return $this;
    }

    /**
     * Remove feedback
     *
     * @param AwardFeedback $feedback
     */
    public function removeFeedback(AwardFeedback $feedback)
    {
        $this->feedback->removeElement($feedback);
    }

    /**
     * Get feedback
     *
     * @return ArrayCollection
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Add nominee
     *
     * @param Nominee $nominee
     *
     * @return Award
     */
    public function addNominee(Nominee $nominee)
    {
        $this->nominees[] = $nominee;

        return $this;
    }

    /**
     * Remove nominee
     *
     * @param Nominee $nominee
     */
    public function removeNominee(Nominee $nominee)
    {
        $this->nominees->removeElement($nominee);
    }

    /**
     * Get nominees
     *
     * @return arrayCollection|Nominee[]
     */
    public function getNominees()
    {
        return $this->nominees;
    }

    /**
     * @param string $shortName
     * @return Nominee
     */
    public function getNominee($shortName)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('shortName', $shortName));

        $nominees = $this->getNominees()->matching($criteria);
        if (count($nominees) === 0) {
            return null;
        } else {
            return $nominees[0];
        }
    }

    /**
     * Add userNomination
     *
     * @param UserNomination $userNomination
     *
     * @return Award
     */
    public function addUserNomination(UserNomination $userNomination)
    {
        $this->userNominations[] = $userNomination;

        return $this;
    }

    /**
     * Remove userNomination
     *
     * @param UserNomination $userNomination
     */
    public function removeUserNomination(UserNomination $userNomination)
    {
        $this->userNominations->removeElement($userNomination);
    }

    /**
     * @return ArrayCollection
     */
    public function getRawUserNominations()
    {
        return $this->userNominations;
    }

    /**
     * @param bool $sortAlphabetically
     * @return array
     */
    public function getUserNominations($sortAlphabetically = false)
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

    /**
     * Add vote
     *
     * @param Vote $vote
     *
     * @return Award
     */
    public function addVote(Vote $vote)
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param Vote $vote
     */
    public function removeVote(Vote $vote)
    {
        $this->votes->removeElement($vote);
    }

    /**
     * Get votes
     *
     * @return arrayCollection
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Add resultCache
     *
     * @param ResultCache $resultCache
     *
     * @return Award
     */
    public function addResultCache(ResultCache $resultCache)
    {
        $this->resultCache[] = $resultCache;

        return $this;
    }

    /**
     * Remove resultCache
     *
     * @param ResultCache $resultCache
     */
    public function removeResultCache(ResultCache $resultCache)
    {
        $this->resultCache->removeElement($resultCache);
    }

    /**
     * Get resultCache
     *
     * @return arrayCollection|ResultCache[]
     */
    public function getResultCache()
    {
        return $this->resultCache;
    }

    /**
     * @return ResultCache|null
     */
    public function getOfficialResults()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('filter', ResultCache::OFFICIAL_FILTER));

        $cache = $this->getResultCache()->matching($criteria);
        if (count($cache) === 0) {
            return null;
        } else {
            return $cache[0];
        }
    }

    /**
     * Set autocompleter
     *
     * @param Autocompleter $autocompleter
     *
     * @return Award
     */
    public function setAutocompleter(Autocompleter $autocompleter = null)
    {
        $this->autocompleter = $autocompleter;

        return $this;
    }

    /**
     * Get autocompleter
     *
     * @return Autocompleter
     */
    public function getAutocompleter()
    {
        return $this->autocompleter;
    }

    public function getGroupedFeedback()
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

    public function getFeedbackPercent()
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

    public function jsonSerialize()
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

    /**
     * @return string
     */
    public function getWinnerImage()
    {
        return $this->winnerImage;
    }

    /**
     * @param string $winnerImage
     * @return Award
     */
    public function setWinnerImage($winnerImage)
    {
        $this->winnerImage = $winnerImage ?: null;
        return $this;
    }

    /**
     * Add suggestion
     *
     * @param AwardSuggestion $suggestion
     *
     * @return Award
     */
    public function addSuggestion(AwardSuggestion $suggestion)
    {
        $this->suggestions[] = $suggestion;

        return $this;
    }

    /**
     * Remove suggestion
     *
     * @param AwardSuggestion $suggestion
     */
    public function removeSuggestion(AwardSuggestion $suggestion)
    {
        $this->suggestions->removeElement($suggestion);
    }

    /**
     * Get suggestions
     *
     * @return ArrayCollection
     */
    public function getRawSuggestions()
    {
        return $this->suggestions;
    }

    /**
     * @param bool $sortAlphabetically
     * @return array
     */
    public function getNameSuggestions($sortAlphabetically = false)
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
}

