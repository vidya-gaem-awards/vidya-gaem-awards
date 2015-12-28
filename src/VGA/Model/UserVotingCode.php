<?php

namespace VGA\Model;

/**
 * UserVotingCode
 */
class UserVotingCode
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $cookieID;

    /**
     * @var string
     */
    private $referer;


    /**
     * Set code
     *
     * @param string $code
     *
     * @return UserVotingCode
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set cookieID
     *
     * @param string $cookieID
     *
     * @return UserVotingCode
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
     * Set referer
     *
     * @param string $referer
     *
     * @return UserVotingCode
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
}

