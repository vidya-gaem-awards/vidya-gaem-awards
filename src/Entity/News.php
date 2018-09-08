<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * News
 */
class News
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $headline;

    /**
     * @var string
     */
    private $text;

    /**
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var boolean
     */
    private $visible = true;

    /**
     * @var \App\Entity\User
     */
    private $user;

    /**
     * @var \App\Entity\User
     */
    private $deletedBy;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set headline
     *
     * @param string $headline
     *
     * @return News
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;

        return $this;
    }

    /**
     * Get headline
     *
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return News
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return News
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return News
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     *
     * @return News
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns true if the post was created in the past two days
     * @return bool
     */
    public function isNew()
    {
        return $this->getTimestamp() > new \DateTime('-2 days');
    }

    /**
     * Returns true if the post was created more than a week ago
     * @return bool
     */
    public function isOld()
    {
        return $this->getTimestamp() < new \DateTime('-7 days');
    }

    /**
     * @return User
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * @param UserInterface $deletedBy
     */
    public function setDeletedBy($deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }
}

