<?php

namespace VGA\Model;

/**
 * Access
 */
class Access
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $cookieID;

    /**
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var string
     */
    private $page;

    /**
     * @var string
     */
    private $requestString;

    /**
     * @var string
     */
    private $requestMethod;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $userAgent;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $referer;

    /**
     * @var \VGA\Model\User
     */
    private $user;

    public function __construct()
    {
        $this->setTimestamp(new \DateTime());
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cookieID
     *
     * @param string $cookieID
     *
     * @return Access
     */
    public function setCookieID($cookieID)
    {
        $this->cookieID = $cookieID;

        return $this;
    }

    /**
     * Get cookieID
     *
     * @return string
     */
    public function getCookieID()
    {
        return $this->cookieID;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return Access
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set page
     *
     * @param string $page
     *
     * @return Access
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set requestString
     *
     * @param string $requestString
     *
     * @return Access
     */
    public function setRequestString($requestString)
    {
        $this->requestString = $requestString;

        return $this;
    }

    /**
     * Get requestString
     *
     * @return string
     */
    public function getRequestString()
    {
        return $this->requestString;
    }

    /**
     * Set requestMethod
     *
     * @param string $requestMethod
     *
     * @return Access
     */
    public function setRequestMethod($requestMethod)
    {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    /**
     * Get requestMethod
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Access
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set userAgent
     *
     * @param string $userAgent
     *
     * @return Access
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get userAgent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return Access
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set referer
     *
     * @param string $referer
     *
     * @return Access
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Get referer
     *
     * @return string
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * Set user
     *
     * @param \VGA\Model\User $user
     *
     * @return Access
     */
    public function setUser(\VGA\Model\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \VGA\Model\User
     */
    public function getUser()
    {
        return $this->user;
    }
}

