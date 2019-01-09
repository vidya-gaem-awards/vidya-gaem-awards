<?php
namespace App\Service;

use App\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;

class ConfigService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Config */
    private $config;

    /** @var PredictionService */
    private $predictionService;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Config
     * @throws \Exception
     */
    public function getConfig()
    {
        if (!isset($this->config)) {
            $this->config = $this->em->getRepository(Config::class)->findOneBy([]);
        }

        if (!$this->config) {
            throw new \Exception('The configuration couldn\'t be loaded from the database.');
        }

        return $this->config;
    }

    /**
     * This is the most commonly used part of the config
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->getConfig()->isReadOnly();
    }
}
