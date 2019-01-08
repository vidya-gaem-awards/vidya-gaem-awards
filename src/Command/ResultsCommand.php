<?php
namespace App\Command;

use App\Entity\Access;
use App\Entity\Award;
use App\Entity\FantasyPrediction;
use App\Entity\FantasyUser;
use App\Entity\ResultCache;
use App\Entity\Vote;
use App\Entity\VotingCodeLog;
use App\Service\ConfigService;
use App\Service\CronJobService;
use App\VGA\ResultCalculator\Schulze;
use App\VGA\Timer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResultsCommand extends Command
{
    const COMMAND_NAME = 'app:results';

    /** @var EntityManagerInterface */
    private $em;

    /** @var ConfigService */
    private $configService;

    /** @var CronJobService */
    private $cron;
    
    /** @var Timer */
    private $timer;
    
    /** @var OutputInterface */
    private $output;

    public function __construct(EntityManagerInterface $em, ConfigService $configService, CronJobService $cron)
    {
        $this->em = $em;
        $this->configService = $configService;
        $this->cron = $cron;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Calculates and stores the results for each award.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->configService->isReadOnly()) {
            throw new \RuntimeException('Database is in read-only mode. Read-only mode must be disabled to run this script.');
        }

        $this->output = $output;
        $this->timer = new Timer();

        $this->updateVoteReferrers();
        $this->updateResultCache();
        $this->updatePredictionScores();
        $this->disableCronJobIfNeeded();
    }
    
    private function updateVoteReferrers()
    {
        // Step 1. Get a list of voters
        $result = $this->em->createQueryBuilder()
            ->select('DISTINCT (v.cookieID) as id')
            ->from(Vote::class, 'v')
            ->getQuery()
            ->getResult();

        $voters = array_fill_keys(array_column($result, 'id'), [
            'codes' => [],
            'notes' => [],
            'referrers' => []
        ]);

        $this->writeln("Step 1 (create array) complete");

        // Step 2. Check voting codes
        $codeLogRepo = $this->em->getRepository(VotingCodeLog::class);

        $result = $codeLogRepo->findAll();

        /** @var VotingCodeLog $row */
        foreach ($result as $row) {
            if (isset($voters[$row->getCookieID()])) {
                $voters[$row->getCookieID()]['codes'][] = $row->getCode();
            }
        }

        $this->writeln("Step 2 (get voting codes) complete");

        // Step 3. Check referrers
        $result = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Access::class, 'a')
            ->where("a.referer NOT LIKE 'https://____.vidyagaemawards.com%'")
            ->orWhere('a.referer IS NULL')
            ->orderBy('a.timestamp', 'ASC')
            ->getQuery()
            ->getResult();

        /** @var Access $access */
        foreach ($result as $access) {
            if (!isset($voters[$access->getCookieID()])) {
                continue;
            }

            $referer = preg_replace('{https?://(www\.)?}', '', $access->getReferer());
            $voters[$access->getCookieID()]['referrers'][] = $referer;
        }

        $this->writeln("Step 3 (get referrers) complete");

        // Step 4. Begin the processing
        $sites = [
            'reddit.com' => 2 ** 0,
            't.co' => 2 ** 1,
            'boards.4chan.org' => 2 ** 2,
            'sys.4chan.org' => 2 ** 2,
            'forums.somethingawful.com' => 2 ** 3,
            'neogaf.com' => 2 ** 4,
            'facepunch.com' => 2 ** 5,
            '8ch.net' => 2 ** 6,
            'twitch.tv' => 2 ** 7,
            'facebook.com' => 2 ** 8,
            'm.facebook.com' => 2 ** 8,
            'l.facebook.com' => 2 ** 8,
            'google.' => 2 ** 9,
            // voting code: 2 ** 10
            // no referer: 2 ** 11,
            'yandex.ru' => 2 ** 12,
            'kiwifarms.net' => 2 ** 13,
        ];

        foreach ($voters as $id => &$info) {
            $number = 0;

            // If user has a voting code
            if (count($info['codes']) > 0) {
                $number += 2 ** 10;
                $info['notes'][] = "Has voting code";
            }

            $referers = array_unique($info['referrers']);

            // It's possible to have multiple unique referrers for one site.
            // To avoid messing up the bitmask, only count each site once.
            $used_bits = [];

            foreach ($referers as $referer) {
                foreach ($sites as $site => $value) {
                    if (self::startsWith($referer, $site) && !in_array($value, $used_bits, true)) {
                        $info['notes'][] = $site;
                        $used_bits[] = $value;
                        $number += $value;
                    }
                }

                if ($referer == '') {
                    $number += 2 ** 11;
                }
            }

            $info['number'] = $number;
        }

        $numberTotals = [];
        foreach ($voters as $info) {
            if (!isset($numberTotals[$info['number']])) {
                $numberTotals[$info['number']] = 0;
            }
            $numberTotals[$info['number']]++;
        }

        $this->writeln("Step 4 (assign numbers) complete");

        // Step 5. Update the values in the database
        $baseQuery = $this->em->createQueryBuilder()
            ->update(Vote::class, 'v')
            ->where('v.cookieID = :id');

        $count = 0;
        foreach ($voters as $id => $info) {
            $count++;

            $query = clone $baseQuery;
            $query
                ->set('v.number', $info['number'])
                ->setParameter('id', $id)
                ->getQuery()
                ->execute();

            if ($count % 1000 == 0) {
                $this->writeln("Processing record $count...");
            }
        }

        $this->writeln("Step 5 (update database) complete");
    }
    
    private function updateResultCache()
    {
        // Remove all existing data
        $this->em->createQueryBuilder()->delete(ResultCache::class)->getQuery()->execute();

        // Start by getting a list of awards and all the nominees.
        $awards = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Award::class, 'a')
            ->where('a.enabled = true')
            ->orderBy('a.order', 'ASC')
            ->getQuery()
            ->getResult();

        $this->writeln("Awards loaded.");

        $filters = [
            '01-all' => false, // no filtering
            '04-4chan' => 'BIT_AND(v.number, 4) > 0',  // 4chan
            '05-4chan-and-voting-code' => 'BIT_AND(v.number, 4) > 0 AND BIT_AND(v.number, 1024) > 0', // 4chan AND voting code
            '06-4chan-without-voting-code' => 'BIT_AND(v.number, 4) > 0 AND NOT BIT_AND(v.number, 1024) > 0', // 4chan AND NOT voting code
            '07-4chan-or-null' => 'BIT_AND(v.number, 4) > 0 OR BIT_AND(v.number, 2048) > 0', // 4chan OR null
            '08-4chan-or-null-with-voting-code' => 'BIT_AND(v.number, 4) > 0 OR (BIT_AND(v.number, 2048) > 0 AND BIT_AND(v.number, 1024) > 0)', // 4chan OR (null AND voting code)
            '09-null-and-voting-code' => 'BIT_AND(v.number, 2048) > 0 AND BIT_AND(v.number, 1024) > 0', // null AND voting code
            '10-null-without-voting-code' => 'BIT_AND(v.number, 2048) > 0 AND NOT BIT_AND(v.number, 1024) > 0',  // null AND NOT voting code
            '11-reddit' => 'BIT_AND(v.number, 1) > 0',
            '12-twitter' => 'BIT_AND(v.number, 2) > 0',
            // 4chan: 4
            '13-something-awful' => 'BIT_AND(v.number, 8) > 0',
            '14-neogaf' => 'BIT_AND(v.number, 16) > 0',
            '15-facepunch' => 'BIT_AND(v.number, 32) > 0',
            '16-8chan' => 'BIT_AND(v.number, 64) > 0',
            '17-twitch' => 'BIT_AND(v.number, 128) > 0',
            '18-facebook' => 'BIT_AND(v.number, 256) > 0',
            '19-google' => 'BIT_AND(v.number, 512) > 0',
            '02-voting-code' => 'BIT_AND(v.number, 1024) > 0',
            '03-null' => 'BIT_AND(v.number, 2048) > 0',
            '20-yandex' => 'BIT_AND(v.number, 4096) > 0',
            '21-kiwifarms' => 'BIT_AND(v.number, 8192) > 0',
        ];

        // Now we can start grabbing votes.
        foreach ($filters as $filterName => $condition) {
            /** @var Award $award */
            foreach ($awards as $award) {

                $query = $this->em->createQueryBuilder()
                    ->select('v.preferences')
                    ->from(Vote::class, 'v')
                    ->join('v.award', 'c')
                    ->where('c.id = :award')
                    ->setParameter('award', $award->getId());

                if ($condition) {
                    $query->andWhere($condition);
                }

                $result = $query->getQuery()->getResult();
                $votes = array_filter(array_column($result, 'preferences'));

                $nominees = [];
                foreach ($award->getNominees() as $nominee) {
                    $nominees[$nominee->getShortName()] = $nominee;
                }

                $resultCalculator = new Schulze($nominees, $votes);
                $result = $resultCalculator->calculateResults();

                $resultObject = new ResultCache();
                $resultObject
                    ->setAward($award)
                    ->setFilter($filterName)
                    ->setResults($result)
                    ->setSteps($resultCalculator->getSteps())
                    ->setWarnings($resultCalculator->getWarnings())
                    ->setVotes(count($votes));
                $this->em->persist($resultObject);

                $this->writeln("[$filterName] Award complete: " . $award->getId());
            }

            // Flush after each filter
            $this->em->flush();
        }

        $this->writeln("Done.");
    }

    private function updatePredictionScores()
    {
        /** @var FantasyUser[] $predictionUser */
        $predictionUsers = $this->em->getRepository(FantasyUser::class)->findAll();

        foreach ($predictionUsers as $predictionUser) {
            $score = 0;
            foreach ($predictionUser->getPredictions() as $prediction) {
                $award = $prediction->getAward();
                $results = $award->getResultCache();
                if (!$results) {
                    continue;
                }

                $winnerID = $results[1];
                if ($winnerID === $prediction->getNominee()->getId()) {
                    $score++;
                }
            }

            $predictionUser->setScore($score);
            $this->em->persist($predictionUser);
        }

        $this->em->flush();
    }

    private function disableCronJobIfNeeded()
    {
        $votingEnd = $this->configService->getConfig()->getVotingEnd();
        if (!$votingEnd) {
            return;
        }

        $votingEnd = \DateTimeImmutable::createFromMutable($votingEnd);
        $votingEnd->modify('+1 day');
        if ($votingEnd < new \DateTime() && $this->cron->isCronJobEnabled()) {
            $this->cron->disableCronJob();
            $this->writeln('Voting has been closed for more than one day - disabling cron job.');
        }
    }

    /**
     * Convenience function to write a line with the timer value.
     * @param string $line
     */
    private function writeln(string $line)
    {
        $this->output->writeln(
            sprintf('%5.2f: %s', $this->timer->time(), $line)
        );
    }

    private static function startsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
