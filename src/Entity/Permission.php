<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Permission
 */
class Permission
{
    const STANDARD_PERMISSIONS = [
        'add_user' => 'Add a new level 1 user',
        'add_video_game' => 'Add a game to the autocomplete list',
        'adverts_manage' => 'View and manage the fake voting page ads',
        'audit_log_view' => 'View the website\'s audit log',
        'awards_delete' => 'Delete awards',
        'awards_edit' => 'Edit award information',
        'awards_feedback' => 'View award voting feedback',
        'awards_secret' => 'View secret awards',
        'edit_config' => 'Edit site config, such as voting times',
        'LEVEL_1' => 'Provides limited access to non-secret data',
        'LEVEL_2' => 'Provides additional read-only access to slightly more information',
        'LEVEL_3' => 'Gives edit access to a number of things',
        'LEVEL_4' => 'Gives access to everything except for critical areas',
        'LEVEL_5' => 'Gives complete admin access',
        'items_manage' => 'View and manage the lootbox rewards',
        'news_manage' => 'Add and delete news items',
        'news_view_user' => 'View the user that posted each news item',
        'nominations_edit' => 'Edit official nominees',
        'nominations_view' => 'View nominees and user nominations',
        'profile_edit_details' => 'Edit user details',
        'profile_edit_groups' => 'Edit user groups',
        'profile_edit_notes' => 'Edit notes attached to user profile',
        'profile_view' => 'View user profiles',
        'referrers_view' => 'View where site visitors are coming from',
        'tasks_nominees' => 'Complete nominee tasks on the tasks page',
        'tasks_view' => 'View the Remaining Tasks page',
        'template_edit' => 'Edit certain pages using the page editor',
        'view_debug_output' => 'Show detailed error messages when something goes wrong',
        'view_unfinished_pages' => 'View some pages before they are ready for the public',
        'voting_code' => 'View voting codes',
        'voting_results' => 'View voting results',
        'voting_view' => 'View the voting page',
    ];

    const STANDARD_PERMISSION_INHERITANCE = [
        'LEVEL_1' => ['add_video_game', 'awards_feedback', 'nominations_view', 'tasks_view', 'view_unfinished_pages', 'voting_view'],
        'LEVEL_2' => ['LEVEL_1', 'awards_secret', 'news_view_user', 'profile_view', 'tasks_nominees', 'voting_code'],
        'LEVEL_3' => ['LEVEL_2', 'awards_edit', 'nominations_edit', 'profile_edit_notes'],
        'LEVEL_4' => ['LEVEL_3', 'add_user', 'audit_log_view', 'news_manage', 'profile_edit_details', 'referrers_view', 'voting_results', 'adverts_manage', 'items_manage'],
        'LEVEL_5' => ['LEVEL_4', 'awards_delete', 'edit_config', 'template_edit', 'profile_edit_groups']
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
     * To avoid unnecessary database calls, we assume a permission can only have children if it's a LEVEL permission.
     * @return ArrayCollection|Permission[]
     */
    public function getChildren()
    {
        if (substr($this->getId(), 0, 5) !== 'LEVEL') {
            return new ArrayCollection();
        }
        return $this->children;
    }

    /**
     * @return ArrayCollection|Permission[]
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

