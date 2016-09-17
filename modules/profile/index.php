<?php
/*
 * mobiCMS Content Management System (http://mobicms.net)
 *
 * For copyright and license information, please see the LICENSE.md
 * Installing the system or redistributions of files must retain the above copyright notice.
 *
 * @link        http://mobicms.net mobiCMS Project
 * @copyright   Copyright (C) mobiCMS Community
 * @license     LICENSE.md (see attached file)
 *
 * @module      User Profile
 * @author      Oleg (AlkatraZ) Kasyanov <dev@mobicms.net>
 * @version     v.1.0.0 2015-02-01
 */

defined('JOHNCMS') or die('Error: restricted access');

$public_actions = [
    'reputation' => 'reputation.php',
];

$personal_actions = [
    'option'                  => 'option.php',
    'option/avatar'           => 'option_avatar.php',
    'option/avatar/animation' => 'option_avatar_animation.php',
    'option/avatar/delete'    => 'option_avatar_delete.php',
    'option/avatar/gravatar'  => 'option_avatar_gravatar.php',
    'option/avatar/image'     => 'option_avatar_image.php',
    'option/edit'             => 'option_edit.php',
    'option/email'            => 'option_email.php',
    'option/language'         => 'option_language.php',
    'option/nickname'         => 'option_nickname.php',
    'option/password'         => 'option_password.php',
    'option/rank'             => 'option_rank.php',
    'option/settings'         => 'option_settings.php',
    'option/theme'            => 'option_theme.php',
];

$app = App::getInstance();
$app->lng()->setModule('profile');
$query = $app->router()->getQuery();
$user = $app->user()->get();
$include = __DIR__ . '/includes/';

if (isset($query[0])) {
    try {
        if (!ctype_digit($query[0]) || $query[0] < 1) {
            throw new Exception('Invalid data');
        }

        $profile = $app->user()->findById($query[0]);
        $app->setService('profile', $profile);

        if (isset($query[1])) {
            $act = implode('/', array_slice($query, 1));
            if (($user->rights == 9 || ($user->rights == 7 && $user->rights > $profile->rights) || ($app->user()->isValid() && $user->id == $profile->id))
                && isset($personal_actions[$act])
            ) {
                $include .= $personal_actions[$act];
            } elseif (isset($public_actions[$act])) {
                $include .= $public_actions[$act];
            } else {
                $include = false;
            }
        } else {
            $include .= 'profile.php';
        }
    } catch (Exception $e) {
        //TODO: Сделать пересылку на ошибку несуществующего юзера
        echo _s('User does not exists');
        exit;
    }
} else {
    $include .= 'index.php';
}

if ($include && is_file($include)) {
    require_once $include;
} else {
    $app->redirect($app->request()->getBaseUrl() . '/404');
}
