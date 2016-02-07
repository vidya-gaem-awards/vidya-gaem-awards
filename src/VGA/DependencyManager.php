<?php
namespace VGA;

use Doctrine\ORM;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
            'dbname' => DB_DATABASE,
            'charset' => 'UTF8'
        ];

        self::$entity_manager = ORM\EntityManager::create($conn, $config);
        return self::$entity_manager;
    }

    public static function getTwig(UrlGeneratorInterface $urlGenerator)
    {
        if (self::$twig) {
            return self::$twig;
        }

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../views');
        $twig = new \Twig_Environment($loader);
        $twig->addExtension(new RoutingExtension($urlGenerator));
        $twig->addExtension(new \Twig_Extensions_Extension_Date());
        $twig->addExtension(new \Twig_Extensions_Extension_Array());

        self::$twig = $twig;
        return $twig;
    }
}
