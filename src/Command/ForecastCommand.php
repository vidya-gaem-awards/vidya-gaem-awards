<?php
namespace App\Command;

use App\Entity\Award;
use App\Entity\ResultCache;
use App\Entity\Vote;
use App\Service\ResultsService;
use App\VGA\Timer;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ForecastCommand extends Command
{
    const COMMAND_NAME = 'app:forecast';

    private Timer $timer;
    private OutputInterface $output;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ResultsService $resultsService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->addOption('not-after', null, InputOption::VALUE_OPTIONAL);
        $this->addOption('format', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->timer = new Timer();

        $format = $input->getOption('format');
        if (!in_array($format, ['tsv', 'tsv2', 'human'])) {
            $format = 'human';
        }

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
            if ($format === 'human') {
                $this->writeln($award->getName());
            }

            $query = $this->em->createQueryBuilder()
                ->select('v.preferences')
                ->from(Vote::class, 'v')
                ->join('v.award', 'c')
                ->where('c.id = :award')
                ->setParameter('award', $award->getId())
                ->andWhere(ResultsCommand::FILTERS[ResultCache::OFFICIAL_FILTER]);

            if ($input->getOption('not-after')) {
                $date = new DateTimeImmutable($input->getOption('not-after'));

                $query
                    ->andWhere('v.timestamp <= :notAfter')
                    ->setParameter('notAfter', $date);
            }

            $preferences = $query
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

            $sweepPoints = $originalResults->getSteps()['sweepPoints'];

            if ($format === 'human') {
                $this->writeln('  1st: ' . $nominees[$first]->getName());
                $this->writeln('  2nd: ' . $nominees[$second]->getName());
                $this->writeln('  Votes required to overtake: ' . $count . ' (' . sprintf('%.2f', $count / $originalVoteCount * 100) . '%)');
                $this->writeln('  Sweep point diff: ' . round($sweepPoints[$first] - $sweepPoints[$second]));
                $this->writeln( '  Iterations: ' . $iterations);
            } elseif ($format === 'tsv') {
                $this->output->writeln(implode("|", [
                    $award->getName(),
                    $nominees[$first]->getName(),
                    $nominees[$second]->getName(),
                    $count,
                    $originalVoteCount,
                ]));
            } elseif ($format === 'tsv2') {
                $this->output->writeln(implode("|", [
                    $nominees[$first]->getName(),
                    $nominees[$second]->getName(),
                    $count,
                    $originalVoteCount,
                ]));
            }
        }

        $this->writeln('Iterations required: ' . $totalIterations);

        return 0;
    }

    /**
     * Convenience function to write a line with the timer value.
     */
    private function writeln(string $line)
    {
        $this->output->writeln(
            sprintf('%5.2f: %s', $this->timer->time(), $line)
        );
    }
}
