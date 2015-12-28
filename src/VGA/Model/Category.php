<?php

namespace VGA\Model;

/**
 * Category
 */
class Category
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $feedback;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $nominees;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userNominations;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $votes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $resultCache;

    /**
     * @var \VGA\Model\Autocompleter
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
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Add feedback
     *
     * @param \VGA\Model\CategoryFeedback $feedback
     *
     * @return Category
     */
    public function addFeedback(\VGA\Model\CategoryFeedback $feedback)
    {
        $this->feedback[] = $feedback;

        return $this;
    }

    /**
     * Remove feedback
     *
     * @param \VGA\Model\CategoryFeedback $feedback
     */
    public function removeFeedback(\VGA\Model\CategoryFeedback $feedback)
    {
        $this->feedback->removeElement($feedback);
    }

    /**
     * Get feedback
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Add nominee
     *
     * @param \VGA\Model\Nominee $nominee
     *
     * @return Category
     */
    public function addNominee(\VGA\Model\Nominee $nominee)
    {
        $this->nominees[] = $nominee;

        return $this;
    }

    /**
     * Remove nominee
     *
     * @param \VGA\Model\Nominee $nominee
     */
    public function removeNominee(\VGA\Model\Nominee $nominee)
    {
        $this->nominees->removeElement($nominee);
    }

    /**
     * Get nominees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNominees()
    {
        return $this->nominees;
    }

    /**
     * Add userNomination
     *
     * @param \VGA\Model\UserNomination $userNomination
     *
     * @return Category
     */
    public function addUserNomination(\VGA\Model\UserNomination $userNomination)
    {
        $this->userNominations[] = $userNomination;

        return $this;
    }

    /**
     * Remove userNomination
     *
     * @param \VGA\Model\UserNomination $userNomination
     */
    public function removeUserNomination(\VGA\Model\UserNomination $userNomination)
    {
        $this->userNominations->removeElement($userNomination);
    }

    /**
     * Get userNominations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserNominations()
    {
        return $this->userNominations;
    }

    /**
     * Add vote
     *
     * @param \VGA\Model\Vote $vote
     *
     * @return Category
     */
    public function addVote(\VGA\Model\Vote $vote)
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param \VGA\Model\Vote $vote
     */
    public function removeVote(\VGA\Model\Vote $vote)
    {
        $this->votes->removeElement($vote);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Add resultCache
     *
     * @param \VGA\Model\ResultCache $resultCache
     *
     * @return Category
     */
    public function addResultCache(\VGA\Model\ResultCache $resultCache)
    {
        $this->resultCache[] = $resultCache;

        return $this;
    }

    /**
     * Remove resultCache
     *
     * @param \VGA\Model\ResultCache $resultCache
     */
    public function removeResultCache(\VGA\Model\ResultCache $resultCache)
    {
        $this->resultCache->removeElement($resultCache);
    }

    /**
     * Get resultCache
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResultCache()
    {
        return $this->resultCache;
    }

    /**
     * Set autocompleter
     *
     * @param \VGA\Model\Autocompleter $autocompleter
     *
     * @return Category
     */
    public function setAutocompleter(\VGA\Model\Autocompleter $autocompleter = null)
    {
        $this->autocompleter = $autocompleter;

        return $this;
    }

    /**
     * Get autocompleter
     *
     * @return \VGA\Model\Autocompleter
     */
    public function getAutocompleter()
    {
        return $this->autocompleter;
    }
}

