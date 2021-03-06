<?php
/** @var Psr\Container\ContainerInterface $container */
$container = App::getContainer();

/** @var Mobicms\Api\ConfigInterface $config */
$config = $container->get(Mobicms\Api\ConfigInterface::class);

$app = App::getInstance();
?>
<!DOCTYPE html>
<html lang="<?= $config->lng ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    <meta name="keywords" content="<?= htmlspecialchars($config->metaKey) ?>"/>
    <meta name="description" content="<?= htmlspecialchars($config->metaDesc) ?>"/>
    <meta name="HandheldFriendly" content="true"/>
    <meta name="MobileOptimized" content="width"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <title><?= isset($this->pagetitle) ? $this->pagetitle : $config->homeTitle ?></title>
    <link rel="shortcut icon" href="<?= $app->image('favicon.ico', [], false, false) ?>"/>
    <link rel="alternate" type="application/rss+xml" title="<?= _s('News') ?>" href="<?= $config->homeUrl ?>/rss"/>
    <link rel="stylesheet" href="<?= $this->getLink('mobicms.min.css') ?>">
    <?= $this->loadHeader() ?>
</head>
<body>
<div class="container">
    <a name="top"></a>

    <!-- Панель навигации -->
    <?php include($this->getPath('include.navbar.php')) ?>

    <!-- Содержимое -->
    <div class="content">
        <?php $this->loadTemplate() ?>
        <?= $this->loadRawContent(true) ?>
    </div>

    <!-- Нижняя панель инструментов -->
    <ul class="bottom">
        <li><a href="<?= $config->homeUrl ?>/online/"><i class="user fw"></i><?= Includes\Counters::usersOnline() ?> :: <?= Includes\Counters::guestsOnline() ?></a></li>
        <li><a href="<?= $config->homeUrl ?>/help/"><i class="life-bouy fw"></i><?= _s('Help') ?></a></li>
        <li><a href="#top"><i class="arrow-up fw"></i><?= _s('Up') ?></a></li>
    </ul>

    <!-- Информация внизу страницы -->
    <div class="text-center text-primary small">
        <div><?= $config->copyright ?></div>
        <div class="profiler">
            <?php if ($config->profilingGeneration): ?>
                <div>Generation: <?= round((microtime(true) - START_TIME), 4) ?> sec</div>
            <?php endif ?>
            <?php if ($config->profilingMemory): ?>
                <div>Memory: <?= round((memory_get_usage() - START_MEMORY) / 1024, 2) ?> kb</div>
            <?php endif ?>
        </div>
        <div><a href="http://mobicms.net">mobiCMS</a></div>
    </div>
</div>
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>-->
<script src="<?= $config->homeUrl ?>/assets/js/jquery-2.1.4.min.js"></script>
<script src="<?= $this->getLink('mobicms.min.js') ?>"></script>
<?= $this->loadFooter() ?>
</body>
</html>
