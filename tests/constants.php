<?php

/**
 * Environment constants every entry point of the application defines.
 *
 * The application is served through `public/index.php` and run from `bin/cron`,
 * both of which point these at the real documentation. A test is neither, and
 * reading the real documentation would have it answer differently every time it
 * is synced, so a test reads the fixtures beside it instead.
 */

require_once __DIR__ . '/../vendor/autoload.php';

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('APP_NAME') || define('APP_NAME', 'Phuture Documentation');
defined('VIEWS_DIR') || define('VIEWS_DIR', dirname(__DIR__) . DS . 'views' . DS);
defined('CACHE_DIR') || define('CACHE_DIR', __DIR__ . DS . 'temp' . DS);
defined('DOCS_DIR') || define('DOCS_DIR', __DIR__ . DS . 'fixtures' . DS . 'docs' . DS);
defined('BASE_PATH') || define('BASE_PATH', '');
