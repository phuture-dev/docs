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
 * Count the urls from where the site is served
 *
 * The router works out the base path of the site by taking the document root off the
 * working directory, and the working directory a web server hands a script is the
 * folder that script sits in. That makes every url count from `/public` wherever the
 * document root is not the public folder itself, which is how a shared host serves a
 * site dropped into the folder it already publishes. The run is moved to the folder
 * the urls are counted from, which is the project root while it sits inside the
 * document root, and the public folder otherwise.
 */
$root = dirname(__DIR__);
$documentRoot = realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''));
$isPublished = $documentRoot !== false && ($root === $documentRoot || str_starts_with($root, $documentRoot . DS));

chdir($isPublished ? $root : __DIR__);

/**
 * The folder of the domain this site is served from, which every url it writes carries
 */
define('BASE_PATH', $isPublished ? str_replace(DS, '/', substr($root, strlen($documentRoot))) : '');

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
