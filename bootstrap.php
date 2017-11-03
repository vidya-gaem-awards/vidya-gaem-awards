<?php
use VGA\DependencyManager;
use AppBundle\Entity\Config;

ini_set('display_errors', 0);
require(__DIR__ . '/vendor/autoload.php');

// The false parameter to getEntityManager is very important: if removed, the timezone of all DateTime objects from
// Doctrine will be the server default instead of what's in our config.
Config::initalizeTimezone(DependencyManager::getEntityManager(false));
