<?php
namespace VGA;

use Doctrine\ORM;

/**
 * A singleton class which allows us to pass around an instance of Doctrine's EntityManager class.
 */
class EntityManager
{
    /** @var ORM\EntityManager */
    private static $entity_manager;

    public static function get()
    {
        if (self::$entity_manager) {
            return self::$entity_manager;
        }

        $isDevMode = true;
        $config = ORM\Tools\Setup::createYAMLMetadataConfiguration([__DIR__ . "/../../config/yaml"], $isDevMode);
        $config->setProxyDir(__DIR__ . '/../VGA/Proxies');
        $config->setProxyNamespace('VGA\\Proxies');

        // database configuration parameters
        $conn = [
            'driver' => 'pdo_mysql',
            'host' => DB_HOST,
            'user' => DB_USER,
            'password' => DB_PASSWORD,
            'dbname' => DB_DATABASE
        ];

        self::$entity_manager = ORM\EntityManager::create($conn, $config);
        return self::$entity_manager;
    }
}
