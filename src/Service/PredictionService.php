<?php
namespace App\Service;

use App\Entity\Award;
use App\Entity\Config;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class PredictionService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ConfigService */
    private $configService;

    public function __construct(EntityManagerInterface $em, ConfigService $configService)
    {
        $this->em = $em;
        $this->configService = $configService;
    }

    public function arePredictionsLocked()
    {
        if ($this->configService->getConfig()->isReadOnly()) {
            return true;
        }

        if (!$this->configService->getConfig()->getStreamTime()) {
            return false;
        }

        return new DateTime('+14 days') > $this->configService->getConfig()->getStreamTime();
    }

    public function areResultsAvailable()
    {
        return $this->configService->getConfig()->isPagePublic('results');
    }

    public function isAwardResultAvailable(Award $award)
    {
        return $this->areResultsAvailable();
    }
}
