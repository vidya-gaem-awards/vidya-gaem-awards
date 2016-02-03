<?php

namespace VGA\Model;

use Doctrine\Common\Collections;

class User
{
    const EVERYONE = '*';
    const LOGGED_IN = 'logged-in';

    /**
     * @var string
     */
    private $steamID;

    /**
     * @var string
     */
    private $name;

    /**
     * @var boolean
     */
    private $special = false;

    /**
     * @var \DateTime
     */
    private $firstLogin;

    /**
     * @var \DateTime
     */
    private $lastLogin;

    /**
     * @var string
     */
    private $primaryRole;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $notes;

    /**
     * @var string
     */
    private $website;

    /**
     * @var string
     */
    private $avatar;

    /**
     * @var LoginToken
     */
    private $loginToken;

    /**
     * @var Collections\Collection
     */
    private $votes;

    /**
     * @var Collections\Collection
     */
    private $permissions;

    /**
     * @var Collections\Collection
     */
    private $logins;

    /**
     * @var string
     */
    private $ipAddress;

    /**
     * @var string
     */
    private $randomID;

    /**
     * @var Collections\Collection|Permission[]
     */
    private $permissionCache;

    /**
     * @var string
     */
    private $votingCode;

    /**
     * @param string $steamID
     */
    public function __construct($steamID)
    {
        $this->steamID = $steamID;
        $this->votes = new Collections\ArrayCollection();
        $this->permissions = new Collections\ArrayCollection();
        $this->logins = new Collections\ArrayCollection();
    }

    /**
     * Set steamID
     *
     * @param string $steamID
     *
     * @return User
     */
    public function setSteamID($steamID)
    {
        $this->steamID = $steamID;

        return $this;
    }

    /**
     * Get steamID
     *
     * @return string
     */
    public function getSteamID()
    {
        return $this->steamID;
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
     * @param \DateTime $firstLogin
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
     * @return \DateTime
     */
    public function getFirstLogin()
    {
        return $this->firstLogin;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
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
     * @return \DateTime
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
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set loginToken
     *
     * @param LoginToken $loginToken
     *
     * @return User
     */
    public function setLoginToken(LoginToken $loginToken = null)
    {
        $this->loginToken = $loginToken;

        return $this;
    }

    /**
     * Get loginToken
     *
     * @return LoginToken
     */
    public function getLoginToken()
    {
        return $this->loginToken;
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
     * @return Collections\Collection
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
     * @return Collections\Collection|Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    private function populatePermissionCache()
    {
        $permissions = new Collections\ArrayCollection();
        foreach ($this->getPermissions() as $permission) {
            if (substr($permission->getId(), 0, 5) !== 'level') {
                $permissions->add($permission);
            }
            foreach ($permission->getChildrenRecurvise() as $child) {
                if (substr($child->getId(), 0, 5) !== 'level') {
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

    public function canDo($permissionCheck)
    {
        if ($permissionCheck === self::EVERYONE) {
            return true;
        }

        if ($permissionCheck === self::LOGGED_IN) {
            return $this->isLoggedIn();
        }

        if ($this->permissionCache === null) {
            $this->populatePermissionCache();
        }

        foreach ($this->permissionCache as $permission) {
            if ($permission->getId() === $permissionCheck) {
                return true;
            }
        }

        return false;
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
     * @return Collections\Collection
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
        return $this->isLoggedIn() ? $this->getSteamID() : $this->getIP();
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
}

