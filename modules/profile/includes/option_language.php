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
 */

defined('JOHNCMS') or die('Error: restricted access');

$app = App::getInstance();
$uri = $app->uri();
$config = $app->profile()->config();
$items['#'] = _m('Select automatically');
$items = array_merge($items, $app->lng()->getLocalesList());
$form = new Mobicms\Form\Form(['action' => $uri]);
$form
    ->title(_m('Select Language'))
    ->element('radio', 'lng',
        [
            'checked'     => $config->lng,
            'items'       => $items,
            'description' => _m('If you turn on automatic mode, the system language is set depending on the settings of your browser.')
        ]
    )
    ->divider()
    ->element('submit', 'submit',
        [
            'value' => _s('Save'),
            'class' => 'btn btn-primary'
        ]
    )
    ->html('<a class="btn btn-link" href="../">' . _s('Back') . '</a>');

if ($form->isValid()) {
    $config->lng = $form->output['lng'];
    $config->save();
    $app->session()->offsetUnset('lng');
    $app->redirect($uri . '?saved');
}

$app->view()->form = $form->display();
$app->view()->setTemplate('edit_form.php');
