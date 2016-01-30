<?php

namespace VGA\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Category
 */
class Category implements \JsonSerializable
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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->feedback = new \Doctrine\Common\Collections\ArrayCollection();
        $this->nominees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userNominations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->votes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->resultCache = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Category
     */
    public function setId($id)
    {
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
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * @return Category
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set nominationsEnabled
     *
     * @param boolean $nominationsEnabled
     *
     * @return Category
     */
    public function setNominationsEnabled($nominationsEnabled)
    {
        $this->nominationsEnabled = $nominationsEnabled;

        return $this;
    }

    /**
     * Get nominationsEnabled
     *
     * @return boolean
     */
    public function getNominationsEnabled()
    {
        return $this->nominationsEnabled;
    }

    /**
     * Set secret
     *
     * @param boolean $secret
     *
     * @return Category
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
     * @param CategoryFeedback $feedback
     *
     * @return Category
     */
    public function addFeedback(CategoryFeedback $feedback)
    {
        $this->feedback[] = $feedback;

        return $this;
    }

    /**
     * Remove feedback
     *
     * @param CategoryFeedback $feedback
     */
    public function removeFeedback(CategoryFeedback $feedback)
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
     * @return Category
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
     * @return arrayCollection
     */
    public function getNominees()
    {
        return $this->nominees;
    }

    /**
     * Add userNomination
     *
     * @param UserNomination $userNomination
     *
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * @return arrayCollection
     */
    public function getResultCache()
    {
        return $this->resultCache;
    }

    /**
     * Set autocompleter
     *
     * @param Autocompleter $autocompleter
     *
     * @return Category
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

    public function getFeedbackPercent()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('opinion', 1));
        $positive = count($this->getFeedback()->matching($criteria));

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('opinion', -1));
        $negative = count($this->getFeedback()->matching($criteria));

        $total = $positive + $negative;

        if ($total === 0) {
            return [
                'positive' => 0,
                'negative' => 0
            ];
        }

        return [
            'positive' => $positive / $total * 100,
            'negative' => $negative / $total * 100
        ];
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'subtitle' => $this->getSubtitle(),
            'autocompleter' => $this->getAutocompleter() ? $this->getAutocompleter()->getId() : $this->getId(),
            'comments' => $this->getComments() ?: '',
            'nominationsEnabled' => $this->getNominationsEnabled()
        ];
    }
}

