<?php
namespace VGA\Model;

use Doctrine\ORM\EntityManager;
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

    const DEFAULT_TIMEZONE = 'America/New_York';

    /** @var string */
    private $id = 1;

    /** @var \DateTime */
    private $votingStart;

    /** @var \DateTime */
    private $votingEnd;

    /** @var \DateTime */
    private $streamTime;

    /** @var string */
    private $defaultPage = 'home';

    /** @var string[] */
    private $publicPages = [];

    /** @var boolean */
    private $readOnly = false;

    /** @var string */
    private $timezone = self::DEFAULT_TIMEZONE;

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

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone Must be a valid timezone identifier
     * @return Config
     * @throws \Exception
     */
    public function setTimezone(string $timezone): Config
    {
        // This will throw an Exception if the timezone is invalid
        new \DateTimeZone($timezone);

        $this->timezone = $timezone;
        return $this;
    }

    /**
     * A convenience method to initialize the timezone.
     * @param EntityManager $em
     */
    public static function initalizeTimezone(EntityManager $em)
    {
        /** @var Config $config */
        $config = $em->getRepository(Config::class)->findOneBy([]);
        if ($config) {
            date_default_timezone_set($config->getTimezone());
        } else {
            date_default_timezone_set(self::DEFAULT_TIMEZONE);
        }
    }
}
