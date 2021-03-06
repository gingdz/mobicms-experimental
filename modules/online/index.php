<?php

defined('MOBICMS') or die('Error: restricted access');

/** @var Psr\Container\ContainerInterface $container */
$container = App::getContainer();

/** @var Mobicms\Api\RouterInterface $router */
$router = $container->get(Mobicms\Api\RouterInterface::class);

$admin_actions = [
    'guests' => 'guests.php',
    'ip'     => 'ip.php'
];

$common_actions = [
    'history' => 'history.php'
];

$app = App::getInstance();
$query = $router->getQuery();
$include = __DIR__ . '/includes/';

if (isset($query[0])) {
    if ($app->user()->get()->rights > 0 && isset($admin_actions[$query[0]])) {
        $include .= $admin_actions[$query[0]];
    } elseif (isset($common_actions[$query[0]])) {
        $include .= $common_actions[$query[0]];
    } else {
        $include = false;
    }
} else {
    $include .= 'index.php';
}

if ($include && is_file($include)) {
    require_once $include;
} else {
    $app->redirect($app->request()->getBaseUrl() . '/404');
}
