<?php

defined('MOBICMS') or die('Error: restricted access');

/** @var Psr\Container\ContainerInterface $container */
$container = App::getContainer();

/** @var Mobicms\Api\ViewInterface $view */
$view = $container->get(Mobicms\Api\ViewInterface::class);

$app = App::getInstance();

// Построение графика репутации
$reputation = !empty($app->profile()->reputation)
    ? unserialize($app->profile()->reputation)
    : ['a' => 0, 'b' => 0, 'c' => 0, 'd' => 0, 'e' => 0];

$array = [];
$total = array_sum($reputation);

foreach ($reputation as $key => $val) {
    $array[$key] = $total
        ? 100 / $total * $val
        : 0;
}

$view->reputation = $array;
$view->reputation_total = $total;
$view->setTemplate('profile.php');
