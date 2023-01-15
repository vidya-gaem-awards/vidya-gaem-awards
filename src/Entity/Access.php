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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="cookie_id", type="string", length=255, nullable=false)
     */
    private string $cookieID;

    /**
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private DateTime $timestamp;

    /**
     * @ORM\Column(name="route", type="string", length=30, nullable=false)
     */
    private string $route;

    /**
     * @ORM\Column(name="controller", type="string", nullable=false)
     */
    private string $controller;

    /**
     * @ORM\Column(name="request_string", type="string", length=255, nullable=false)
     */
    private string $requestString;

    /**
     * @ORM\Column(name="request_method", type="string", length=4, nullable=false)
     */
    private string $requestMethod;

    /**
     * @ORM\Column(name="ip", type="string", length=45, nullable=false)
     */
    private string $ip;

    /**
     * @ORM\Column(name="user_agent", type="string", length=255, nullable=false)
     */
    private string $userAgent;

    /**
     * @ORM\Column(name="filename", type="string", length=255, nullable=false)
     */
    private string $filename;

    /**
     * @ORM\Column(name="referer", type="string", length=255, nullable=true)
     */
    private ?string $referer;

    /**
     * @ORM\Column(name="headers", type="json", nullable=true)
     */
    private ?array $headers = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="id", nullable=true)
     * })
     */
    private ?User $user;

    public function __construct()
    {
        $this->setTimestamp(new DateTime());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setCookieID(string $cookieID): Access
    {
        $this->cookieID = $cookieID;

        return $this;
    }

    public function getCookieID(): string
    {
        return $this->cookieID;
    }

    public function setTimestamp(DateTime $timestamp): Access
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setRoute(string $route): Access
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setController(string $controller): Access
    {
        $this->controller = $controller;

        return $this;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function setRequestString(string $requestString): Access
    {
        $this->requestString = substr($requestString, 0, 255);
        return $this;
    }

    public function getRequestString(): string
    {
        return $this->requestString;
    }

    public function setRequestMethod(string $requestMethod): Access
    {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function setIp(string $ip): Access
    {
        $this->ip = $ip;
        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setUserAgent(string $userAgent): Access
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setFilename(string $filename): Access
    {
        $this->filename = $filename;
        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setReferer(?string $referer): Access
    {
        $this->referer = mb_substr($referer, 0, 190);
        return $this;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function setUser(?User $user): Access
    {
        $this->user = $user;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setHeaders(array $headers): Access
    {
        $this->headers = $headers;
        return $this;
    }

    public function getHeaders(): ?array
    {
        return $this->headers;
    }
}

