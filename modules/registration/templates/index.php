<?php
/** @var Mobicms\Api\ConfigInterface $config */
$config = App::getContainer()->get(Mobicms\Api\ConfigInterface::class);
?>
<!-- Заголовок раздела -->
<div class="titlebar">
    <div><h1><?= _s('Registration') ?></h1></div>
</div>

<!-- Форма -->
<div class="content box padding">
    <?php if ($config->registrationAllow): ?>
        <?= $this->form ?>
    <?php else: ?>
        <div class="alert alert-danger text-center">
            <?= _s('Registration is temporarily closed') ?>
        </div>
    <?php endif ?>
</div>
