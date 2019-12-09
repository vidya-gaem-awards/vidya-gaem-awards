<?php
namespace App\Entity;

use DateTime;
use DateTimeZone;
use Exception;
use Moment\Moment;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="config", options={"collate"="utf8mb4_unicode_ci","charset"="utf8mb4"})
 * @ORM\Entity
 */
class Config
{
    const ALLOWED_DEFAULT_PAGES = [
        'home' => 'Standard landing page',
        'awards' => 'Awards and Nominations',
        'voting' => 'Voting page',
        'countdown' => 'Stream countdown',
        'stream' => 'Stream page',
        'finished' => 'Post-stream "thank you" page',
        'promo' => 'Special promo page',
    ];

    const DEFAULT_TIMEZONE = 'America/New_York';

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=30)
     * @ORM\Id
     */
    private $id = 1;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="voting_start", type="datetime", nullable=true)
     */
    private $votingStart;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="voting_end", type="datetime", nullable=true)
     */
    private $votingEnd;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="stream_time", type="datetime", nullable=true)
     */
    private $streamTime;

    /**
     * @var string
     *
     * @ORM\Column(name="default_page", type="string", length=30, nullable=false)
     */
    private $defaultPage = 'home';

    /**
     * @var bool
     *
     * @ORM\Column(name="award_suggestions", type="boolean", nullable=false)
     */
    private $awardSuggestions = true;

    /**
     * @var array
     *
     * @ORM\Column(name="public_pages", type="json", nullable=false)
     */
    private $publicPages = [];

    /**
     * @var bool
     *
     * @ORM\Column(name="read_only", type="boolean", nullable=false)
     */
    private $readOnly = false;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=50, nullable=false)
     */
    private $timezone = self::DEFAULT_TIMEZONE;

    /**
     * @var array
     *
     * @ORM\Column(name="navbar_items", type="json", nullable=false)
     */
    private $navbarItems = ['config' => ['label' => 'Config', 'order' => 1]];

    /**
     * @return DateTime
     */
    public function getVotingStart()
    {
        return $this->votingStart;
    }

    /**
     * @param DateTime $votingStart
     * @return Config
     */
    public function setVotingStart($votingStart)
    {
        $this->votingStart = $votingStart;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getVotingEnd()
    {
        return $this->votingEnd;
    }

    /**
     * @param DateTime $votingEnd
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
        $now = new DateTime();

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
        $now = new DateTime();

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
        $now = new DateTime();

        if ($this->getVotingEnd() && $now > $this->getVotingEnd()) {
            return true;
        }

        return false;
    }

    public static function getRelativeTimeString(DateTime $date)
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
     * @return DateTime
     */
    public function getStreamTime()
    {
        return $this->streamTime;
    }

    /**
     * @param DateTime $streamTime
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
        if ($page === 'home' || $page === 'promo') {
            return true;
        }

        // Some conditionally public pages have multiple routes with the same permission.
        // For these alternate routes, just check the main route.
        $alternateRoutes = [
            'awardFrontendPost' => 'awards',
            'votingSubmission' => 'voting',
            'voteWithCode' => 'voting',
            'predictions' => 'voting',
            'predictionRules' => 'voting',
            'predictionJoin' => 'voting',
            'predictionUpdatePick' => 'voting',
            'predictionUpdateDetails' => 'voting',
            'predictionRedirect' => 'voting',
            'predictionLeaderboard' => 'results',
            'pairwiseResults' => 'results',
            'winners' => 'results',
        ];

        if (isset($alternateRoutes[$page])) {
            $page = $alternateRoutes[$page];
        }

        return in_array($page, $this->getPublicPages(), true);
    }

    /**
     * @return string[]
     */
    public function getPublicPages(): array
    {
        return $this->publicPages;
    }

    /**
     * @param string[] $publicPages
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
     * @throws Exception
     */
    public function setTimezone(string $timezone): Config
    {
        // This will throw an Exception if the timezone is invalid
        new DateTimeZone($timezone);

        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getNavbarItems(): array
    {
        uasort($this->navbarItems, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });
        return $this->navbarItems;
    }

    /**
     * @param array $navbarItems
     * @return Config
     */
    public function setNavbarItems(array $navbarItems): Config
    {
        $this->navbarItems = $navbarItems;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAwardSuggestions(): bool
    {
        return $this->awardSuggestions;
    }

    /**
     * @param bool $awardSuggestions
     * @return Config
     */
    public function setAwardSuggestions(bool $awardSuggestions): Config
    {
        $this->awardSuggestions = $awardSuggestions;
        return $this;
    }
}
