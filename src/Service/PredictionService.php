<?php
namespace App\Service;

use DateTime;

class PredictionService
{
    private ConfigService $configService;

    public function __construct(ConfigService $configService)
    {
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
}
