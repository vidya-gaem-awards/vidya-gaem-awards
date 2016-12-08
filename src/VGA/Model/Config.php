<?php
namespace VGA\Model;

use Moment\Moment;

class Config
{
    const ALLOWED_DEFAULT_PAGES = [
        'home' => 'Standard landing page',
        'awards' => 'Awards and Nominations',
        'voting' => 'Voting page',
        'countdown' => 'Stream countdown',
        'stream' => 'Stream page',
        'finished' => 'Post-stream "thank you" page',
    ];

    /** @var string */
    private $id;

    /** @var \DateTime */
    private $votingStart;

    /** @var \DateTime */
    private $votingEnd;

    /** @var \DateTime */
    private $streamTime;

    /** @var string */
    private $defaultPage;

    /** @var string[] */
    private $publicPages;

    /** @var boolean */
    private $readOnly;

    /**
     * @return \DateTime
     */
    public function getVotingStart()
    {
        return $this->votingStart;
    }

    /**
     * @param \DateTime $votingStart
     * @return Config
     */
    public function setVotingStart($votingStart)
    {
        $this->votingStart = $votingStart;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getVotingEnd()
    {
        return $this->votingEnd;
    }

    /**
     * @param \DateTime $votingEnd
     * @return Config
     */
    public function setVotingEnd($votingEnd)
    {
        $this->votingEnd = $votingEnd;
        return $this;
    }

    /**
     * @return bool Returns true if voting hasn't yet opened.
     */
    public function isVotingNotYetOpen()
    {
        $now = new \DateTime();

        if (!$this->getVotingStart() || $now < $this->getVotingStart()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool Returns true if voting is currently open.
     */
    public function isVotingOpen()
    {
        $now = new \DateTime();

        if (!$this->getVotingStart() || $now < $this->getVotingStart()) {
            return false;
        }

        if ($this->getVotingEnd() && $now > $this->getVotingEnd()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool Returns true if voting has closed.
     */
    public function hasVotingClosed()
    {
        $now = new \DateTime();

        if ($this->getVotingEnd() && $now > $this->getVotingEnd()) {
            return true;
        }

        return false;
    }

    public static function getRelativeTimeString(\DateTime $date)
    {
        $moment = new Moment($date->format('c'));
        $diff = $moment->fromNow()->setDirection('+');

        if ($diff->getSeconds() <= 120) {
            return (int)$diff->getSeconds() . ' second' . ((int)$diff->getSeconds() === 1 ? '' : 's');
        } elseif ($diff->getMinutes() <= 120) {
            return (int)$diff->getMinutes() . ' minutes';
        } elseif ($diff->getHours() <= 48) {
            return (int)$diff->getHours() . ' hours';
        } else {
            return (int)$diff->getDays() . ' days';
        }
    }

    /**
     * @return \DateTime
     */
    public function getStreamTime()
    {
        return $this->streamTime;
    }

    /**
     * @param \DateTime $streamTime
     * @return Config
     */
    public function setStreamTime($streamTime)
    {
        $this->streamTime = $streamTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultPage()
    {
        return $this->defaultPage;
    }

    /**
     * @param string $defaultPage
     * @return Config
     */
    public function setDefaultPage($defaultPage)
    {
        if (self::ALLOWED_DEFAULT_PAGES[$defaultPage] ?? false) {
            $this->defaultPage = $defaultPage;
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function getAllowedDefaultPages()
    {
        return self::ALLOWED_DEFAULT_PAGES;
    }

    /**
     * @param boolean $readOnly
     * @return Config
     */
    public function setReadOnly(bool $readOnly): Config
    {
        $this->readOnly = $readOnly;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * @param string $page
     * @return bool
     */
    public function isPagePublic(string $page): bool
    {
        return in_array($page, $this->getPublicPages(), true);
    }

    /**
     * @return \string[]
     */
    public function getPublicPages(): array
    {
        return $this->publicPages;
    }

    /**
     * @param \string[] $publicPages
     * @return Config
     */
    public function setPublicPages(array $publicPages): Config
    {
        $this->publicPages = $publicPages;
        return $this;
    }
}
