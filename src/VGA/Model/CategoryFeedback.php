<?php

namespace VGA\Model;

/**
 * CategoryFeedback
 */
class CategoryFeedback
{
    /**
     * @var string
     */
    private $user;

    /**
     * @var integer
     */
    private $opinion;

    /**
     * @var \VGA\Model\Category
     */
    private $category;


    /**
     * Set user
     *
     * @param string $user
     *
     * @return CategoryFeedback
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
     * Set opinion
     *
     * @param integer $opinion
     *
     * @return CategoryFeedback
     */
    public function setOpinion($opinion)
    {
        $this->opinion = $opinion;

        return $this;
    }

    /**
     * Get opinion
     *
     * @return integer
     */
    public function getOpinion()
    {
        return $this->opinion;
    }

    /**
     * Set category
     *
     * @param \VGA\Model\Category $category
     *
     * @return CategoryFeedback
     */
    public function setCategory(\VGA\Model\Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \VGA\Model\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}

