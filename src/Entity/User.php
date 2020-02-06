<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections;
use Doctrine\Common\Collections\Collection;
use Knojector\SteamAuthenticationBundle\User\AbstractSteamUser;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="steam_id", columns={"steam_id"})})
 * @ORM\Entity
 */
class User extends AbstractSteamUser implements UserInterface
{
    const EVERYONE = '*';
    const LOGGED_IN = 'logged-in';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="steam_id", type="string", length=17)
     */
    protected $steamId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="special", type="boolean", nullable=false)
     */
    private $special = false;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="firstLogin", type="datetime", nullable=true)
     */
    private $firstLogin;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="lastLogin", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="primaryRole", type="string", length=255, nullable=true)
     */
    private $primaryRole;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="website", type="string", length=40, nullable=true)
     */
    private $website;

    /**
     * @var string|null
     *
     * @ORM\Column(name="avatar", type="text", nullable=true)
     */
    protected $avatar;

    /**
     * @var FantasyUser
     *
     * @ORM\OneToOne(targetEntity="App\Entity\FantasyUser", mappedBy="user")
     */
    private $fantasyUser;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="user")
     */
    private $votes;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Login", mappedBy="user")
     */
    private $logins;

    /**
     * @var Collection
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
    private $permissions;

    /**
     * @var string
     */
    private $ipAddress;

    /**
     * @var string
     */
    private $randomID;

    /**
     * @var Collection|Permission[]
     */
    private $permissionCache;

    /**
     * @var string
     */
    private $votingCode;

    /*
     * The properties below are from the AbstractSteamUser class.
     * We don't use any of them, but we have to declare them here because otherwise Doctrine
     * will read the annotations from the base class and try to create database columns for them.
     */
    protected $communityVisibilityState;
    protected $profileState;
    protected $profileName;
    protected $lastLogOff;
    protected $commentPermission;
    protected $profileUrl;
    protected $personaState;
    protected $primaryClanId;
    protected $joinDate;
    protected $countryCode;
    protected $roles;

    public function __construct()
    {
        $this->votes = new Collections\ArrayCollection();
        $this->permissions = new Collections\ArrayCollection();
        $this->logins = new Collections\ArrayCollection();
    }

    /*
     * The getSteamId function provided by the AbstractSteamUser class returns the steam ID as an integer.
     * This can result in a loss of precision if you're using a system or language that doesn't support 64 bit integers
     * (as a Steam ID takes up a bit more than 56 bits). Unfortunately, one of these languages happens to be JavaScript,
     * which only supports 53 bits of precision. Given that we use JavaScript extensively, we need a function that
     * returns the steam ID as a string instead, which completely bypasses the issue.
     */
    public function getSteamIdString(): string
    {
        return $this->steamId;
    }

    public function setSteamId(int $steamID)
    {
        $this->steamId = $steamID;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * Set special
     *
     * @param boolean $special
     *
     * @return User
     */
    public function setSpecial($special)
    {
        $this->special = $special;

        return $this;
    }

    /**
     * Get special
     *
     * @return boolean
     */
    public function isSpecial()
    {
        return $this->special;
    }

    /**
     * Set firstLogin
     *
     * @param DateTime $firstLogin
     *
     * @return User
     */
    public function setFirstLogin($firstLogin)
    {
        $this->firstLogin = $firstLogin;

        return $this;
    }

    /**
     * Get firstLogin
     *
     * @return DateTime
     */
    public function getFirstLogin()
    {
        return $this->firstLogin;
    }

    /**
     * Set lastLogin
     *
     * @param DateTime $lastLogin
     *
     * @return User
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set primaryRole
     *
     * @param string $primaryRole
     *
     * @return User
     */
    public function setPrimaryRole($primaryRole)
    {
        $this->primaryRole = $primaryRole;

        return $this;
    }

    /**
     * Get primaryRole
     *
     * @return string
     */
    public function getPrimaryRole()
    {
        return $this->primaryRole;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return User
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set website
     *
     * @param string $website
     *
     * @return User
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar(string $avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * Add vote
     *
     * @param Vote $vote
     *
     * @return User
     */
    public function addVote(Vote $vote)
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param Vote $vote
     */
    public function removeVote(Vote $vote)
    {
        $this->votes->removeElement($vote);
    }

    /**
     * Get votes
     *
     * @return Collection
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Add permission
     *
     * @param Permission $permission
     *
     * @return User
     */
    public function addPermission(Permission $permission)
    {
        $this->permissions[] = $permission;

        return $this;
    }

    /**
     * Remove permission
     *
     * @param Permission $permission
     */
    public function removePermission(Permission $permission)
    {
        $this->permissions->removeElement($permission);
    }

    /**
     * Get permissions
     *
     * @return Collection|Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    private function populatePermissionCache()
    {
        $permissions = new Collections\ArrayCollection();
        foreach ($this->getPermissions() as $permission) {
            $permissions->add($permission);
            foreach ($permission->getChildrenRecurvise() as $child) {
                if (substr($child->getId(), 0, 5) !== 'LEVEL') {
                    $permissions->add($child);
                }
            }
        }

        $this->permissionCache = $permissions;
    }

    public function getAllPermissions()
    {
        if ($this->permissionCache === null) {
            $this->populatePermissionCache();
        }

        return $this->permissionCache;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return true;
    }

    /**
     * @param string $ipAddress
     * @return User
     */
    public function setIP($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getIP()
    {
        return $this->ipAddress;
    }

    /**
     * @param string $randomID
     * @return User
     */
    public function setRandomID($randomID)
    {
        $this->randomID = $randomID;
        return $this;
    }

    /**
     * @return string
     */
    public function getRandomID()
    {
        return $this->randomID;
    }

    /**
     * Add login
     *
     * @param Login $login
     *
     * @return User
     */
    public function addLogin(Login $login)
    {
        $login->setUser($this);
        $this->logins[] = $login;

        return $this;
    }

    /**
     * Remove login
     *
     * @param Login $login
     */
    public function removeLogin(Login $login)
    {
        $this->logins->removeElement($login);
    }

    /**
     * Get logins
     *
     * @return Collection
     */
    public function getLogins()
    {
        return $this->logins;
    }

    /**
     * A fuzzy ID will be either a user ID (for logged in users) or an IP address (for anonymous users).
     * @return string
     */
    public function getFuzzyID()
    {
        return $this->isLoggedIn() ? $this->getSteamIdString() : $this->getIP();
    }

    /**
     * @return mixed
     */
    public function getVotingCode()
    {
        return $this->votingCode;
    }

    /**
     * @param mixed $votingCode
     * @return User
     */
    public function setVotingCode($votingCode)
    {
        $this->votingCode = $votingCode;
        return $this;
    }

    /**
     * Returns the current Steam nickname of the user.
     *
     * @return string The current nickname
     */
    public function getNickname()
    {
        return $this->getName();
    }

    /**
     * Sets the users current nickname.
     *
     * @param string $nickname
     */
    public function setNickname($nickname)
    {
        $this->setName($nickname);
    }

    /**
     * Sets the username.
     *
     * The username represents the unique SteamID.
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->setSteamId($username);
    }

    /**
     * Sets the password.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        // Do nothing
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

        return $roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getSteamIdString();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // Do nothing
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * The authentication API uses this function to set the name
     * @param string $name
     */
    public function setProfileName(string $name)
    {
        $this->profileName = $this->name = $name;
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

    public function setProfileState(?int $state)
    {
        $this->profileState = $state;
    }

    public function setLastLogOff(?int $lastLogOff)
    {
        if ($lastLogOff) {
            parent::setLastLogOff($lastLogOff);
        }
    }
}

