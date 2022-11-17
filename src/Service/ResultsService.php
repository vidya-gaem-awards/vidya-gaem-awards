<?php
namespace App\Service;

use App\Entity\Award;
use App\Entity\ResultCache;
use App\VGA\ResultCalculator\Schulze;
use Doctrine\ORM\EntityManagerInterface;
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
     * @return ResultCache
     */
    public function getResultsForAward(Award $award, array $votePreferences): ResultCache
    {
        $nominees = [];
        foreach ($award->getNominees() as $nominee) {
            $nominees[$nominee->getShortName()] = $nominee;
        }

        $resultCalculator = new Schulze($nominees, $votePreferences);

        $resultObject = new ResultCache();
        $resultObject
            ->setAward($award)
            ->setResults($resultCalculator->calculateResults())
            ->setSteps($resultCalculator->getSteps())
            ->setWarnings($resultCalculator->getWarnings())
            ->setVotes(count($votePreferences));

        return $resultObject;
    }
}
