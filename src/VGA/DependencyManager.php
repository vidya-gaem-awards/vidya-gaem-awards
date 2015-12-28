<?php
namespace VGA;

use Doctrine\ORM;

class DependencyManager
{
    /** @var \PDO */
    private static $database_handle;

    /** @var ORM\EntityManager */
    private static $entity_manager;

    /** @var \Twig_Environment */
    private static $twig;

    public static function getEntityManager()
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

    public static function getDatabaseHandle()
    {
        if (self::$database_handle) {
            return self::$database_handle;
        }

        $server = DB_HOST;
        $username = DB_USER;
        $password = DB_PASSWORD;
        $database = DB_DATABASE;

        $dbh = new \PDO(
            "mysql:host=$server;
            dbname=$database;
            charset=UTF8",
            $username,
            $password
        );
        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
        $dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        self::$database_handle = $dbh;
        return $dbh;
    }

    public static function getTwig()
    {
        if (self::$twig) {
            return self::$twig;
        }

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../views');
        $twig = new \Twig_Environment($loader);

        self::$twig = $twig;
        return $twig;
    }
}
