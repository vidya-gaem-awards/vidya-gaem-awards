<?php
namespace AppBundle\Command;

use AppBundle\Entity\Access;
use AppBundle\Entity\Vote;
use AppBundle\Entity\VotingCodeLog;
use AppBundle\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VGA\Timer;

class VoteReferrersCommand extends ContainerAwareCommand
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
            ->setName('app:vote-referrers')
            ->setDescription('Calculates and applies referrer numbers to each vote.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->configService->isReadOnly()) {
            throw new \RuntimeException('Database is in read-only mode. Read-only mode must be disabled to run this script.');
        }
        
        $timer = new Timer();

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

        $output->writeln("Step 1 (create array) complete: " . $timer->time());

        // Step 2. Check voting codes
        $codeLogRepo = $this->em->getRepository(VotingCodeLog::class);

        $result = $codeLogRepo->findAll();

        /** @var VotingCodeLog $row */
        foreach ($result as $row) {
            if (isset($voters[$row->getCookieID()])) {
                $voters[$row->getCookieID()]['codes'][] = $row->getCode();
            }
        }

        $output->writeln("Step 2 (get voting codes) complete: " . $timer->time());

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

        $output->writeln("Step 3 (get referrers) complete: " . $timer->time());

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

        $output->writeln("Step 4 (assign numbers) complete: " . $timer->time());

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
                $output->writeln("Processing record $count... " . $timer->time());
            }
        }

        $output->writeln("Step 5 (update database) complete: " . $timer->time());
    }

    private static function startsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
