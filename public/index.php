<?php

/**
 * Initialize Composer Autoloader
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Define environment constants
 */
define('DS', DIRECTORY_SEPARATOR);
define('VIEWS_DIR', dirname(__DIR__) . DS . 'views' . DS);
define('CACHE_DIR', dirname(__DIR__) . DS . 'cache' . DS);
define('DOCS_DIR', dirname(__DIR__) . DS . 'docs' . DS);

/**
 * Initialize application
 */
$app = new Pop\Application();

/**
 * Load configuration files
 */
foreach (glob(__DIR__ . '/../config/*.php') as $config) {
    require_once $config;
}

/**
 * Run application
 */
$app->run();
