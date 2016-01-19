<?php

namespace VGA\Model;

class CategoryFeedback
{
    /** @var string */
    private $user;

    /** @var integer */
    private $opinion;

    /** @var Category */
    private $category;

    /**
     * @param Category $category
     * @param User $user
     */
    public function __construct(Category $category, User $user)
    {
        $this->category = $category;
        $this->user = $user->getFuzzyID();
    }

    /**
     * @param string $user
     * @return CategoryFeedback
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param integer $opinion
     * @return CategoryFeedback
     */
    public function setOpinion($opinion)
    {
        $this->opinion = $opinion;

        return $this;
    }

    /**
     * @return integer
     */
    public function getOpinion()
    {
        return $this->opinion;
    }

    /**
     * @param Category $category
     * @return CategoryFeedback
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}

