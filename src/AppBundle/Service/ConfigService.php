<?php
namespace AppBundle\Service;

use AppBundle\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;

class ConfigService
{
    /** @var EntityManagerInterface */
    private $em;

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
        $config = $this->em->getRepository(Config::class)->findOneBy([]);
        if (!$config) {
            throw new \Exception('The configuration couldn\'t be loaded from the database.');
        }
        return $config;
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
