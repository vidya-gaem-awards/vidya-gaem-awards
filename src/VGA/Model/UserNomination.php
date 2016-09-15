<?php
namespace VGA\Model;

class UserNomination
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $nomination;

    /**
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var Category
     */
    private $category;

    /**
     * @param Category $category
     * @param User $user
     * @param string $nomination
     */
    public function __construct(Category $category, User $user, string $nomination)
    {
        $this->category = $category;
        $this->user = $user->getFuzzyID();
        $this->nomination = $nomination;
        $this->timestamp = new \DateTime();
    }

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
     * Set user
     *
     * @param string $user
     *
     * @return UserNomination
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set nomination
     *
     * @param string $nomination
     *
     * @return UserNomination
     */
    public function setNomination($nomination)
    {
        $this->nomination = $nomination;

        return $this;
    }

    /**
     * Get nomination
     *
     * @return string
     */
    public function getNomination()
    {
        return $this->nomination;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return UserNomination
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
     * Set category
     *
     * @param Category $category
     *
     * @return UserNomination
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}

