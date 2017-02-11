#!/usr/bin/env php
<?php
use VGA\DependencyManager;
use VGA\Utils;

require(__DIR__ . '/../bootstrap.php');

Utils::initializeDatabase(DependencyManager::getEntityManager());

echo "Database successfully initialized.\n";
