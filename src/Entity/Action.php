<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="actions")
 * @ORM\Entity
 */
class Action
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="ip", type="string", length=45, nullable=false)
     */
    private string $ip;

    /**
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private DateTime $timestamp;

    /**
     * @ORM\Column(name="page", type="string", length=100, nullable=false)
     */
    private string $page;

    /**
     * @ORM\Column(name="action", type="string", length=40, nullable=false)
     */
    private string $action;

    /**
     * @ORM\Column(name="data1", type="string", length=255, nullable=true)
     */
    private ?string $data1;

    /**
     * @ORM\Column(name="data2", type="string", length=255, nullable=true)
     */
    private ?string $data2;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TableHistory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="history_id", referencedColumnName="id", unique=true, nullable=true)
     * })
     */
    private ?TableHistory $tableHistory;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="id", nullable=true)
     * })
     */
    private ?User $user;

    public function __construct($action, $data1 = null, $data2 = null)
    {
        $backtrace = debug_backtrace();
        $this->setPage($backtrace[1]['class'] . '::' . $backtrace[1]['function']);

        $this->setAction($action);
        $this->setTimestamp(new DateTime());
        $this->setData1($data1);
        $this->setData2($data2);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setUser(BaseUser $user): Action
    {
        $this->ip = $user->getIP();
        if ($user instanceof User) {
            $this->user = $user;
        }
        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setTimestamp(DateTime $timestamp): Action
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setPage(string $page): Action
    {
        $this->page = $page;

        return $this;
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function setAction(string $action): Action
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setData1(?string $data1): Action
    {
        $this->data1 = $data1;

        return $this;
    }

    public function getData1(): ?string
    {
        return $this->data1;
    }

    public function setData2(?string $data2): Action
    {
        $this->data2 = $data2;

        return $this;
    }

    public function getData2(): ?string
    {
        return $this->data2;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getTableHistory(): ?TableHistory
    {
        return $this->tableHistory;
    }

    public function setTableHistory(?TableHistory $tableHistory): Action
    {
        $this->tableHistory = $tableHistory;

        return $this;
    }
}

