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
use App\Service\ResultsService;
use App\VGA\ResultCalculator\Schulze;
use App\VGA\Timer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ForecastCommand extends Command
{
    const COMMAND_NAME = 'app:forecast';

    const FILTERS = [
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
        '15-knockout' => 'BIT_AND(v.number, 32) > 0',
        '16-8chan' => 'BIT_AND(v.number, 64) > 0',
        '17-twitch' => 'BIT_AND(v.number, 128) > 0',
        '18-facebook' => 'BIT_AND(v.number, 256) > 0',
        '19-google' => 'BIT_AND(v.number, 512) > 0',
        '02-voting-code' => 'BIT_AND(v.number, 1024) > 0',
        '03-null' => 'BIT_AND(v.number, 2048) > 0',
        '20-yandex' => 'BIT_AND(v.number, 4096) > 0',
        '21-kiwifarms' => 'BIT_AND(v.number, 8192) > 0',
    ];

    /** @var EntityManagerInterface */
    private $em;

    /** @var ConfigService */
    private $configService;

    /** @var ResultsService */
    private $resultsService;

    /** @var Timer */
    private $timer;

    /** @var OutputInterface */
    private $output;

    public function __construct(EntityManagerInterface $em, ConfigService $configService, ResultsService $resultsService)
    {
        $this->em = $em;
        $this->configService = $configService;
        $this->resultsService = $resultsService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->timer = new Timer();

        $awards = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Award::class, 'a')
            ->where('a.enabled = true')
            ->orderBy('a.order', 'ASC')
            ->getQuery()
            ->getResult();

        $totalIterations = 0;

        /** @var Award $award */
        foreach ($awards as $award) {
            $this->writeln($award->getName());

            $preferences = $this->em->createQueryBuilder()
                ->select('v.preferences')
                ->from(Vote::class, 'v')
                ->join('v.award', 'c')
                ->where('c.id = :award')
                ->setParameter('award', $award->getId())
                ->andWhere(ResultsCommand::FILTERS[ResultCache::OFFICIAL_FILTER])
                ->getQuery()
                ->getResult();

            $nominees = [];
            foreach ($award->getNominees() as $nominee) {
                $nominees[$nominee->getShortName()] = $nominee;
            }

            $originalPreferences = array_filter(array_column($preferences, 'preferences'));
            $results = $originalResults = $this->resultsService->getResultsForAward($award, $originalPreferences);
            $originalVoteCount = $results->getVotes();

            if ($originalVoteCount == 0) {
                continue;
            }

            $first = $results->getResults()[1];
            $second = $results->getResults()[2];

            $count = 0;

            $threshold = $nextThreshold = ceil($originalVoteCount / 2);
            $iterations = 0;

            while ($results->getResults()[1] === $first || $threshold != 1) {
                $threshold = $nextThreshold;
                $count += $threshold;
                $preferences = $originalPreferences;

                $threshold = max(1, $threshold);

//                $this->writeln('  Trying ' . $count . ' (threshold = ' . $threshold . ')');

                for ($i = 0; $i < $count; $i++) {
                    $preferences[] = [1 => $second];
                }

                $results = $this->resultsService->getResultsForAward($award, $preferences);

                if ($results->getResults()[1] !== $first && $threshold != 1) {
                    $count -= $threshold;
                    $nextThreshold = ceil($threshold / 2);
                }

                $iterations++;
                $totalIterations++;
            }

            $this->writeln('  1st: ' . $nominees[$first]->getName());
            $this->writeln('  2nd: ' . $nominees[$second]->getName());
            $this->writeln('  Votes required to overtake: ' . $count . ' (' . sprintf('%.2f', $count / $originalVoteCount * 100) . '%)');

            $sweepPoints = $originalResults->getSteps()['sweepPoints'];
            $this->writeln('  Sweep point diff: ' . round($sweepPoints[$first] - $sweepPoints[$second]));
            $this->writeln( '  Iterations: ' . $iterations);
        }

        $this->writeln('Iterations required: ' . $totalIterations);

        return 0;
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
}
