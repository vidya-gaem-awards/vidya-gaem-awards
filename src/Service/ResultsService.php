<?php
namespace App\Service;

use App\Entity\Award;
use App\Entity\ResultCache;
use App\VGA\AbstractResultCalculator;
use App\VGA\ResultCalculator\Schulze;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class ResultsService
{
    private EntityManagerInterface $em;
    private ConfigService $configService;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, ConfigService $configService, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->configService = $configService;
        $this->logger = $logger;
    }

    /**
     * @param Award $award
     * @param array[] $votePreferences
     * @param class-string<AbstractResultCalculator> $calculator
     * @return ResultCache
     */
    public function getResultsForAward(Award $award, array $votePreferences, string $calculator = Schulze::class): ResultCache
    {
        $nominees = [];
        foreach ($award->getNominees() as $nominee) {
            $nominees[$nominee->getShortName()] = $nominee;
        }

        if (!$calculator instanceof AbstractResultCalculator) {
            throw new InvalidArgumentException('Invalid result calculator class provided: ' . $calculator);
        }

        $resultCalculator = new $calculator($nominees, $votePreferences);

        $resultObject = new ResultCache();
        $resultObject
            ->setAlgorithm($resultCalculator->getAlgorithmId())
            ->setAward($award)
            ->setResults($resultCalculator->calculateResults())
            ->setSteps($resultCalculator->getSteps())
            ->setWarnings($resultCalculator->getWarnings())
            ->setVotes(count($votePreferences));

        return $resultObject;
    }
}
