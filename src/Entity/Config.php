<?php
namespace App\Entity;

use Carbon\CarbonImmutable;
use DateTime;
use DateTimeZone;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'config', options: ['collate' => 'utf8mb4_unicode_ci', 'charset' => 'utf8mb4'])]
#[ORM\Entity]
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

    #[ORM\Column(name: 'id', type: 'string', length: 30)]
    #[ORM\Id]
    private int $id = 1;

    #[ORM\Column(name: 'voting_start', type: 'datetime', nullable: true)]
    private ?DateTime $votingStart;

    #[ORM\Column(name: 'voting_end', type: 'datetime', nullable: true)]
    private ?DateTime $votingEnd;

    #[ORM\Column(name: 'stream_time', type: 'datetime', nullable: true)]
    private ?DateTime $streamTime;

    #[ORM\Column(name: 'default_page', type: 'string', length: 30, nullable: false)]
    private string $defaultPage = 'home';

    #[ORM\Column(name: 'award_suggestions', type: 'boolean', nullable: false)]
    private bool $awardSuggestions = true;

    #[ORM\Column(name: 'public_pages', type: 'json', nullable: false)]
    private array $publicPages = [];

    #[ORM\Column(name: 'read_only', type: 'boolean', nullable: false)]
    private bool $readOnly = false;

    #[ORM\Column(name: 'timezone', type: 'string', length: 50, nullable: false)]
    private string $timezone = self::DEFAULT_TIMEZONE;

    #[ORM\Column(name: 'navbar_items', type: 'json', nullable: false)]
    private array $navbarItems = ['config' => ['label' => 'Config', 'order' => 1]];

    public function getVotingStart(): ?DateTime
    {
        return $this->votingStart;
    }

    public function setVotingStart(?DateTime $votingStart): Config
    {
        $this->votingStart = $votingStart;
        return $this;
    }

    public function getVotingEnd(): ?DateTime
    {
        return $this->votingEnd;
    }

    public function setVotingEnd(?DateTime $votingEnd): Config
    {
        $this->votingEnd = $votingEnd;
        return $this;
    }

    /**
     * @return bool Returns true if voting hasn't yet opened.
     */
    public function isVotingNotYetOpen(): bool
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
    public function isVotingOpen(): bool
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
    public function hasVotingClosed(): bool
    {
        $now = new DateTime();

        if ($this->getVotingEnd() && $now > $this->getVotingEnd()) {
            return true;
        }

        return false;
    }

    public static function getRelativeTimeString(DateTime $date): string
    {
        $carbon = new CarbonImmutable($date);
        $diff = $carbon->diffAsCarbonInterval();

        if ($diff->totalSeconds <= 120) {
            return (int)$diff->totalSeconds . ' second' . ((int)$diff->totalSeconds === 1 ? '' : 's');
        } elseif ($diff->totalMinutes <= 120) {
            return (int)$diff->totalMinutes . ' minutes';
        } elseif ($diff->totalHours <= 48) {
            return (int)$diff->totalHours . ' hours';
        } else {
            return (int)$diff->totalDays . ' days';
        }
    }

    public function getStreamTime(): ?DateTime
    {
        return $this->streamTime;
    }

    public function setStreamTime(?DateTime $streamTime): Config
    {
        $this->streamTime = $streamTime;
        return $this;
    }

    public function getDefaultPage(): string
    {
        return $this->defaultPage;
    }

    public function setDefaultPage(string $defaultPage): Config
    {
        if (self::ALLOWED_DEFAULT_PAGES[$defaultPage] ?? false) {
            $this->defaultPage = $defaultPage;
        }
        return $this;
    }

    public function getAllowedDefaultPages(): array
    {
        return self::ALLOWED_DEFAULT_PAGES;
    }

    public function setReadOnly(bool $readOnly): Config
    {
        $this->readOnly = $readOnly;
        return $this;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

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
     */
    public function setPublicPages(array $publicPages): Config
    {
        $this->publicPages = $publicPages;
        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone Must be a valid timezone identifier
     */
    public function setTimezone(string $timezone): Config
    {
        // This will throw an Exception if the timezone is invalid
        new DateTimeZone($timezone);

        $this->timezone = $timezone;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNavbarItems(): array
    {
        uasort($this->navbarItems, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });
        return $this->navbarItems;
    }

    public function setNavbarItems(array $navbarItems): Config
    {
        $this->navbarItems = $navbarItems;
        return $this;
    }

    public function getAwardSuggestions(): bool
    {
        return $this->awardSuggestions;
    }

    public function setAwardSuggestions(bool $awardSuggestions): Config
    {
        $this->awardSuggestions = $awardSuggestions;
        return $this;
    }
}
