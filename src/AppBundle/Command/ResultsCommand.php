<?php
namespace AppBundle\Command;

use AppBundle\Entity\Award;
use AppBundle\Entity\ResultCache;
use AppBundle\Entity\Vote;
use AppBundle\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VGA\ResultCalculator\Schulze;
use VGA\Timer;

class ResultsCommand extends ContainerAwareCommand
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ConfigService */
    private $configService;

    public function __construct(EntityManagerInterface $em, ConfigService $configService)
    {
        $this->em = $em;
        $this->configService = $configService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:results')
            ->setDescription('Calculates and stores the results for each award.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->configService->isReadOnly()) {
            throw new \RuntimeException('Database is in read-only mode. Read-only mode must be disabled to run this script.');
        }

        $timer = new Timer();

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

        echo $timer->time() . ": awards loaded.\n";

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

                $output->writeln($timer->time() . ": [$filterName] Award complete: " . $award->getId());
            }

            // Flush after each filter
            $this->em->flush();
        }

        $output->writeln($timer->time() . ": done.");
    }
}
