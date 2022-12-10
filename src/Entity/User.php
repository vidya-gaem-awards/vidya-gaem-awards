<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="steam_id", columns={"steam_id"})})
 * @ORM\Entity
 */
class User extends BaseUser
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="steam_id", type="string", length=17)
     */
    protected string $steamId;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="special", type="boolean", nullable=false)
     */
    private bool $special = false;

    /**
     * @ORM\Column(name="firstLogin", type="datetime", nullable=true)
     */
    private ?DateTime $firstLogin = null;

    /**
     * @ORM\Column(name="lastLogin", type="datetime", nullable=true)
     */
    private ?DateTime $lastLogin = null;

    /**
     * @ORM\Column(name="primaryRole", type="string", length=255, nullable=true)
     */
    private ?string $primaryRole = null;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private ?string $notes = null;

    /**
     * @ORM\Column(name="website", type="string", length=40, nullable=true)
     */
    private ?string $website = null;

    /**
     * @ORM\Column(name="avatar", type="text", nullable=true)
     */
    protected ?string $avatar = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FantasyUser", mappedBy="user")
     */
    private ?FantasyUser $fantasyUser = null;

    /**
     * @var Collection<array-key, Vote>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="user")
     */
    private Collection $votes;

    /**
     * @var Collection<array-key, Login>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Login", mappedBy="user")
     */
    private Collection $logins;

    /**
     * @var Collection<array-key, Permission>
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Permission", inversedBy="users")
     * @ORM\JoinTable(name="user_permissions",
     *   joinColumns={
     *     @ORM\JoinColumn(name="userID", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="permissionID", referencedColumnName="id")
     *   }
     * )
     */
    private Collection $permissions;

    /**
     * @var Collection<array-key, Permission>|null
     */
    private ?Collection $permissionCache = null;

    public function __construct()
    {
        $this->votes = new Collections\ArrayCollection();
        $this->permissions = new Collections\ArrayCollection();
        $this->logins = new Collections\ArrayCollection();
    }

    public function getSteamId(): string
    {
        return $this->steamId;
    }

    /**
     * The getSteamId function provided by the AbstractSteamUser class returns the steam ID as an integer.
     * This can result in a loss of precision if you're using a system or language that doesn't support 64-bit integers
     * (as a Steam ID takes up a bit more than 56 bits). Unfortunately, one of these languages happens to be JavaScript,
     * which only supports 53 bits of precision. Given that we use JavaScript extensively, we need a function that
     * returns the steam ID as a string instead, which completely bypasses the issue.
     */
    public function getSteamIdString(): string
    {
        return $this->steamId;
    }

    public function setSteamId(string $steamID): static
    {
        $this->steamId = $steamID;
        return $this;
    }

    public function setName(string $name): User
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setSpecial(bool $special): User
    {
        $this->special = $special;

        return $this;
    }

    public function isSpecial(): bool
    {
        return $this->special;
    }

    public function setFirstLogin(?DateTime $firstLogin): User
    {
        $this->firstLogin = $firstLogin;

        return $this;
    }

    public function getFirstLogin(): ?DateTime
    {
        return $this->firstLogin;
    }

    public function setLastLogin(?DateTime $lastLogin): User
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    public function setPrimaryRole(?string $primaryRole): User
    {
        $this->primaryRole = $primaryRole;

        return $this;
    }

    public function getPrimaryRole(): ?string
    {
        return $this->primaryRole;
    }

    public function setEmail(?string $email): User
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setNotes(?string $notes): User
    {
        $this->notes = $notes;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setWebsite(?string $website): User
    {
        $this->website = $website;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setAvatar(?string $avatar): User
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function addVote(Vote $vote): User
    {
        $this->votes[] = $vote;

        return $this;
    }

    public function removeVote(Vote $vote)
    {
        $this->votes->removeElement($vote);
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addPermission(Permission $permission): User
    {
        $this->permissions[] = $permission;

        return $this;
    }

    public function removePermission(Permission $permission)
    {
        $this->permissions->removeElement($permission);
    }

    /**
     * @return Collection<array-key, Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    private function populatePermissionCache()
    {
        /** @var Collection<array-key, Permission> $permissions */
        $permissions = new ArrayCollection();
        foreach ($this->getPermissions() as $permission) {
            $permissions->add($permission);
            foreach ($permission->getChildrenRecurvise() as $child) {
                if (!str_starts_with($child->getId(), 'LEVEL')) {
                    $permissions->add($child);
                }
            }
        }

        $this->permissionCache = $permissions;
    }

    public function getAllPermissions(): Collection
    {
        if ($this->permissionCache === null) {
            $this->populatePermissionCache();
        }

        return $this->permissionCache;
    }

    public function isLoggedIn(): bool
    {
        return true;
    }

    public function addLogin(Login $login): User
    {
        $login->setUser($this);
        $this->logins[] = $login;

        return $this;
    }

    public function removeLogin(Login $login)
    {
        $this->logins->removeElement($login);
    }

    /**
     * @return Collection<array-key, Login>
     */
    public function getLogins(): Collection
    {
        return $this->logins;
    }

    /**
     * A fuzzy ID will be either a user ID (for logged-in users) or an IP address (for anonymous users).
     */
    public function getFuzzyID(): string
    {
        return $this->getSteamId();
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[] The user roles
     */
    public function getRoles(): array
    {
        $roles = [];

        if ($this->permissionCache === null) {
            $this->populatePermissionCache();
        }

        foreach ($this->permissionCache as $permission) {
            $roles[] = 'ROLE_' . strtoupper($permission->getId());
        }

        // Symfony requires at least one role for a user to be authenticated
        $roles[] = 'ROLE_USER';

        return $roles;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUserIdentifier(): string
    {
        return $this->getSteamId();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFantasyUser(): ?FantasyUser
    {
        return $this->fantasyUser;
    }

    public function setFantasyUser(?FantasyUser $fantasyUser): self
    {
        $this->fantasyUser = $fantasyUser;

        // set (or unset) the owning side of the relation if necessary
        $newUser = $fantasyUser === null ? null : $this;
        if ($newUser !== $fantasyUser->getUser()) {
            $fantasyUser->setUser($newUser);
        }

        return $this;
    }
}

