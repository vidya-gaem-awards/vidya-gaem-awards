<?php

namespace VGA\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Permission
 */
class Permission
{
    const STANDARD_PERMISSIONS = [
        'add-user' => 'Add a new level 1 user',
        'add-video-game' => 'Add a game to the autocomplete list',
        'awards-delete' => 'Delete awards',
        'awards-edit' => 'Edit award information',
        'awards-feedback' => 'View award voting feedback',
        'awards-secret' => 'View secret awards',
        'edit-config' => 'Edit site config, such as voting times',
        'level1' => 'Provides limited access to non-secret data',
        'level2' => 'Provides additional read-only access to slightly more information',
        'level3' => 'Gives edit access to a number of things',
        'level4' => 'Gives access to everything except for critical areas',
        'level5' => 'Gives complete admin access',
        'news-manage' => 'Add and delete news items',
        'news-view-user' => 'View the user that posted each news item',
        'nominations-edit' => 'Edit official nominees',
        'nominations-view' => 'View nominees and user nominations',
        'profile-edit-details' => 'Edit user details',
        'profile-edit-groups' => 'Edit user groups',
        'profile-edit-notes' => 'Edit notes attached to user profile',
        'profile-view' => 'View user profiles',
        'referrers-view' => 'View where site visitors are coming from',
        'view-debug-output' => 'Show detailed error messages when something goes wrong',
        'view-unfinished-pages' => 'View some pages before they are ready for the public',
        'voting-code' => 'View voting codes',
        'voting-results' => 'View voting results',
        'voting-view' => 'View the voting page',
    ];

    const STANDARD_PERMISSION_INHERITANCE = [
        'level1' => ['add-video-game', 'awards-feedback', 'nominations-view', 'view-unfinished-pages', 'voting-view'],
        'level2' => ['level1', 'awards-secret', 'news-view-user', 'profile-view', 'voting-code'],
        'level3' => ['level2', 'awards-edit', 'nominations-edit', 'profile-edit-notes'],
        'level4' => ['level3', 'add-user', 'news-manage', 'profile-edit-details', 'referrers-view', 'voting-results'],
        'level5' => ['level4', 'awards-delete', 'edit-config', 'profile-edit-groups']
    ];

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
     * @param Permission $child
     *
     * @return Permission
     */
    public function addChild(Permission $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param Permission $child
     */
    public function removeChild(Permission $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return Collection
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
     * @param User $user
     *
     * @return Permission
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return Collection
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

