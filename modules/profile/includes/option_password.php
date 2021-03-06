<?php

defined('MOBICMS') or die('Error: restricted access');

/** @var Psr\Container\ContainerInterface $container */
$container = App::getContainer();

/** @var Mobicms\Api\ViewInterface $view */
$view = $container->get(Mobicms\Api\ViewInterface::class);

$app = App::getInstance();
$user = $app->user()->get();
$profile = $app->profile();
$form = new Mobicms\Form\Form(['action' => $app->uri()]);
$form
    ->title(_m('Change Password'))
    ->element('password', 'oldpass',
        [
            'label'    => ($profile->id == $user->id ? _m('Old Password') : _m('Admin Password')),
            'required' => true
        ]
    )
    ->element('password', 'newpass',
        [
            'label'       => _m('New Password'),
            'description' => _s('The password length min. 3 characters'),
            'required'    => true
        ]
    )
    ->element('password', 'newconf',
        [
            'label'    => _s('Repeat password'),
            'required' => true
        ]
    )
    ->divider()
    ->element('submit', 'submit',
        [
            'value' => _s('Save'),
            'class' => 'btn btn-primary'
        ]
    )
    ->html('<a class="btn btn-link" href="../">' . _s('Back') . '</a>')
    ->validate('oldpass', 'lenght', ['continue' => false, 'min' => 3])
    ->validate('newpass', 'lenght', ['continue' => false, 'min' => 3])
    ->validate('newconf', 'compare', ['compare_field' => 'newpass', 'error' => _s("Passwords don't coincide")]);

if ($form->isValid()) {
    if ($user->checkPassword($form->output['oldpass'])) {
        $profile->setPassword($form->output['newpass']);
        $profile->setToken($app->user()->generateToken());
        $profile->save();

        if ($profile->id == $user->id) {
            $remember = filter_has_var(INPUT_COOKIE, $app->user()->domain);
            $app->user()->login($user->nickname, $form->output['newpass'], $remember);
        }

        $form->continueLink = '../';
        $form->successMessage = _m('The password is successfully changed');
        $form->confirmation = true;
    } else {
        $form->setError('oldpass', _s('Invalid password'));
    }
}

$view->form = $form->display();
$view->setTemplate('edit_form.php');
