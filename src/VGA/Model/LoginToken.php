<?php

namespace VGA\Model;

/**
 * LoginToken
 */
class LoginToken
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $avatar;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \DateTime
     */
    private $generated;

    /**
     * @var \DateTime
     */
    private $expires;

    /**
     * @var User
     */
    private $user;

    public function __construct()
    {
        $this->generated = new \DateTime();
        $this->expires = new \DateTime('+30 days');
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return LoginToken
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
     * Set avatar
     *
     * @param string $avatar
     *
     * @return LoginToken
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
     * Set token
     *
     * @param string $token
     *
     * @return LoginToken
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set generated
     *
     * @param \DateTime $generated
     *
     * @return LoginToken
     */
    public function setGenerated($generated)
    {
        $this->generated = $generated;

        return $this;
    }

    /**
     * Get generated
     *
     * @return \DateTime
     */
    public function getGenerated()
    {
        return $this->generated;
    }

    /**
     * Set expires
     *
     * @param \DateTime $expires
     *
     * @return LoginToken
     */
    public function setExpiry($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Get expires
     *
     * @return \DateTime
     */
    public function getExpiry()
    {
        return $this->expires;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return LoginToken
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}

