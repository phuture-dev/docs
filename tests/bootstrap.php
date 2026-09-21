<?php

/**
 * Everything a test file needs before it runs.
 *
 * Loads the autoloader through the constants every test reads the fixtures with,
 * and hands Nette Tester the environment it wants, which is what lets a failed
 * assertion say where it came from.
 */

require_once __DIR__ . '/constants.php';

Tester\Environment::setup();
