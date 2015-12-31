<?php

namespace VGA\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Permission
 */
class Permission
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $description;

    /**
     * @var ArrayCollection|Permission[]
     */
    private $children;

    /**
     * @var ArrayCollection|Permission[]
     */
    private $parents;

    /**
     * @var ArrayCollection|User[]
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Permission
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
     * Set description
     *
     * @param string $description
     *
     * @return Permission
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add child
     *
     * @param \VGA\Model\Permission $child
     *
     * @return Permission
     */
    public function addChild(\VGA\Model\Permission $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \VGA\Model\Permission $child
     */
    public function removeChild(\VGA\Model\Permission $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return ArrayCollection|Permission
     */
    public function getChildrenRecurvise()
    {
        $permissions = new ArrayCollection();

        foreach ($this->getChildren() as $child) {
            foreach ($child->getChildrenRecurvise() as $grandchild) {
                $permissions->add($grandchild);
            }
            $permissions->add($child);
        }

        return $permissions;
    }

    /**
     * Add user
     *
     * @param \VGA\Model\User $user
     *
     * @return Permission
     */
    public function addUser(\VGA\Model\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \VGA\Model\User $user
     */
    public function removeUser(\VGA\Model\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return ArrayCollection|Permission[]
     */
    public function getParents()
    {
        return $this->parents;
    }

    public function __toString()
    {
        return $this->getId();
    }
}

