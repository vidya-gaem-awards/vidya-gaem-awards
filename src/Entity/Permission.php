<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="permissions", options={"collate"="utf8mb4_unicode_ci","charset"="utf8mb4"})
 * @ORM\Entity
 */
class Permission
{
    const STANDARD_PERMISSIONS = [
        'add_user' => 'Add a new level 1 user',
        'add_video_game' => 'Add a game to the autocomplete list',
        'adverts_manage' => 'View and manage the fake voting page ads',
        'arg_manage' => 'View and manage things relating to the ARG',
        'audit_log_view' => 'View the website\'s audit log',
        'autocompleter_edit' => 'Edit nomination autocompleters',
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
        'LEVEL_3' => ['LEVEL_2', 'autocompleter_edit', 'awards_edit', 'nominations_edit', 'profile_edit_notes'],
        'LEVEL_4' => ['LEVEL_3', 'add_user', 'arg_manage', 'audit_log_view', 'news_manage', 'profile_edit_details', 'referrers_view', 'voting_results', 'adverts_manage', 'items_manage'],
        'LEVEL_5' => ['LEVEL_4', 'awards_delete', 'edit_config', 'template_edit', 'profile_edit_groups']
    ];

    /**
     * @ORM\Column(name="id", type="string", length=40)
     * @ORM\Id
     */
    private string $id;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @var Collection<array-key, Permission>
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Permission", inversedBy="parents")
     * @ORM\JoinTable(name="permission_children",
     *   joinColumns={
     *     @ORM\JoinColumn(name="parentID", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="childID", referencedColumnName="id")
     *   }
     * )
     */
    private Collection $children;

    /**
     * @var Collection<array-key, Permission>
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Permission", mappedBy="children")
     */
    private Collection $parents;

    /**
     * @var Collection<array-key, User>
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="permissions")
     */
    private Collection $users;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function setId(string $id): Permission
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setDescription(string $description): Permission
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function addChild(Permission $child): Permission
    {
        $this->children[] = $child;

        return $this;
    }

    public function removeChild(Permission $child): void
    {
        $this->children->removeElement($child);
    }

    /**
     * To avoid unnecessary database calls, we assume a permission can only have children if it's a LEVEL permission.
     * @return Collection<array-key, Permission>
     */
    public function getChildren(): Collection
    {
        if (!str_starts_with($this->getId(), 'LEVEL')) {
            return new ArrayCollection();
        }
        return $this->children;
    }

    /**
     * @return Collection<array-key, Permission>
     */
    public function getChildrenRecurvise(): Collection
    {
        /** @var Collection<array-key, Permission> $permissions */
        $permissions = new ArrayCollection();

        foreach ($this->getChildren() as $child) {
            foreach ($child->getChildrenRecurvise() as $grandchild) {
                $permissions->add($grandchild);
            }
            $permissions->add($child);
        }

        return $permissions;
    }

    public function addUser(User $user): Permission
    {
        $this->users[] = $user;

        return $this;
    }

    public function removeUser(User $user): void
    {
        $this->users->removeElement($user);
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection<array-key, Permission>
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }

    public function __toString()
    {
        return $this->getId();
    }
}

