<?php

if (!isset($app)) {
    $app = new Pop\Application();
}

$app->get('/', ['controller' => 'Phuture\App\Controller\Index', 'action' => 'index']);
$app->get('/search', ['controller' => 'Phuture\App\Controller\Search', 'action' => 'index']);
$app->get('/:page*', ['controller' => 'Phuture\App\Controller\Docs', 'action' => 'index']);
