<?php
namespace App\Service;

use App\Entity\Award;
use App\Entity\Config;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class PredictionService
{
    private EntityManagerInterface $em;
    private ConfigService $configService;

    public function __construct(EntityManagerInterface $em, ConfigService $configService)
    {
        $this->em = $em;
        $this->configService = $configService;
    }

    public function arePredictionsLocked(): bool
    {
        if ($this->configService->getConfig()->isReadOnly()) {
            return true;
        }

        if (!$this->configService->getConfig()->getStreamTime()) {
            return false;
        }

        return new DateTime('+14 days') > $this->configService->getConfig()->getStreamTime();
    }

    public function areResultsAvailable(): bool
    {
        return $this->configService->getConfig()->isPagePublic('results');
    }

    public function isAwardResultAvailable(Award $award): bool
    {
        return $this->areResultsAvailable();
    }
}
