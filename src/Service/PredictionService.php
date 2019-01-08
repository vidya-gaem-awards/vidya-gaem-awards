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

    /** @var Config */
    private $config;

    public function __construct(EntityManagerInterface $em, ConfigService $configService)
    {
        $this->em = $em;
        $this->config = $configService->getConfig();
    }

    public function arePredictionsLocked()
    {
        if ($this->config->isReadOnly()) {
            return true;
        }

        if (!$this->config->getStreamTime()) {
            return false;
        }

        return new DateTime('+14 days') > $this->config->getStreamTime();
    }

    public function areResultsAvailable()
    {
        return $this->config->isPagePublic('results');
    }

    public function isAwardResultAvailable(Award $award)
    {
        return $this->areResultsAvailable();
    }
}
