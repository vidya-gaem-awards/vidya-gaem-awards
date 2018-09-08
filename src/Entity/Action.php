<?php

namespace App\Entity;

/**
 * Action
 */
class Action
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $ip;

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
    private $action;

    /**
     * @var string
     */
    private $data1;

    /**
     * @var string
     */
    private $data2;

    /**
     * @var User
     */
    private $user;

    /**
     * @var TableHistory
     */
    private $tableHistory;

    public function __construct($action, $data1 = null, $data2 = null)
    {
        $backtrace = debug_backtrace();
        $this->setPage($backtrace[1]['class'] . '::' . $backtrace[1]['function']);

        $this->setAction($action);
        $this->setTimestamp(new \DateTime());
        $this->setData1($data1);
        $this->setData2($data2);
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
     * Set user
     *
     * @param User $user
     *
     * @return Action
     */
    public function setUser($user)
    {
        $this->ip = $user->getIP();
        if ($user->isLoggedIn()) {
            $this->user = $user;
        }
        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return Action
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
     * @return Action
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
     * Set action
     *
     * @param string $action
     *
     * @return Action
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set data1
     *
     * @param string $data1
     *
     * @return Action
     */
    public function setData1($data1)
    {
        $this->data1 = $data1;

        return $this;
    }

    /**
     * Get data1
     *
     * @return string
     */
    public function getData1()
    {
        return $this->data1;
    }

    /**
     * Set data2
     *
     * @param string $data2
     *
     * @return Action
     */
    public function setData2($data2)
    {
        $this->data2 = $data2;

        return $this;
    }

    /**
     * Get data2
     *
     * @return string
     */
    public function getData2()
    {
        return $this->data2;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return TableHistory
     */
    public function getTableHistory(): ?TableHistory
    {
        return $this->tableHistory;
    }

    /**
     * @param TableHistory $tableHistory
     */
    public function setTableHistory(?TableHistory $tableHistory)
    {
        $this->tableHistory = $tableHistory;
    }
}

