<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="access")
 * @ORM\Entity
 */
class Access
{
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
     * @ORM\Column(name="cookie_id", type="string", length=255, nullable=false)
     */
    private $cookieID;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=30, nullable=false)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(name="controller", type="string", nullable=false)
     */
    private $controller;

    /**
     * @var string
     *
     * @ORM\Column(name="request_string", type="string", length=255, nullable=false)
     */
    private $requestString;

    /**
     * @var string
     *
     * @ORM\Column(name="request_method", type="string", length=4, nullable=false)
     */
    private $requestMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=45, nullable=false)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="string", length=255, nullable=false)
     */
    private $userAgent;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=false)
     */
    private $filename;

    /**
     * @var string|null
     *
     * @ORM\Column(name="referer", type="string", length=255, nullable=true)
     */
    private $referer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="id", nullable=true)
     * })
     */
    private $user;

    public function __construct()
    {
        $this->setTimestamp(new DateTime());
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $cookieID
     * @return Access
     */
    public function setCookieID($cookieID): Access
    {
        $this->cookieID = $cookieID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCookieID(): string
    {
        return $this->cookieID;
    }

    /**
     * @param DateTime $timestamp
     * @return Access
     */
    public function setTimestamp($timestamp): Access
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param string $route
     * @return Access
     */
    public function setRoute($route): Access
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $controller
     * @return Access
     */
    public function setController($controller): Access
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $requestString
     * @return Access
     */
    public function setRequestString($requestString): Access
    {
        $this->requestString = substr($requestString, 0, 255);
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestString(): string
    {
        return $this->requestString;
    }

    /**
     * @param string $requestMethod
     * @return Access
     */
    public function setRequestMethod($requestMethod): Access
    {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * @param string $ip
     * @return Access
     */
    public function setIp($ip): Access
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $userAgent
     * @return Access
     */
    public function setUserAgent($userAgent): Access
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $filename
     * @return Access
     */
    public function setFilename($filename): Access
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $referer
     * @return Access
     */
    public function setReferer($referer): Access
    {
        $this->referer = mb_substr($referer, 0, 190);
        return $this;
    }

    /**
     * @return string
     */
    public function getReferer(): string
    {
        return $this->referer;
    }

    /**
     * @param User $user
     * @return Access
     */
    public function setUser(User $user = null): Access
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}

