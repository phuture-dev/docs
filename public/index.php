<?php

/**
 * Initialize Composer Autoloader
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Keep what goes wrong to the log rather than to the page
 *
 * A message meant for whoever wrote the site says where the site lives and what it
 * was doing, which is nothing a reader needs and something an attacker is glad of.
 * It is written to the log instead, and the reader is answered with the status and
 * a line saying as much. Set `APP_DEBUG` in the environment to see it on the page
 * while working on the site.
 */
$isDebug = filter_var((string) getenv('APP_DEBUG'), FILTER_VALIDATE_BOOL);

error_reporting(E_ALL);
ini_set('display_errors', $isDebug ? '1' : '0');
ini_set('log_errors', '1');

set_exception_handler(function (Throwable $exception) use ($isDebug): void {
    error_log('Uncaught ' . $exception::class . ': ' . $exception->getMessage()
        . ' in ' . $exception->getFile() . ':' . $exception->getLine());

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }

    echo $isDebug
        ? '<pre>' . htmlspecialchars((string) $exception, ENT_QUOTES | ENT_HTML5) . '</pre>'
        : '<h1>Something went wrong</h1><p>The page could not be built. Please try again later.</p>';
});

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
