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

defined('MOBICMS') or die('Error: restricted access');

use Config\System as Config;

$app = App::getInstance();
$form = new Mobicms\Form\Form(['action' => $app->uri()]);
$form
    ->title(_m('Upload image'))
    ->element('hidden', 'MAX_FILE_SIZE', ['value' => (Config::$filesize * 1024)])
    ->element('file', 'image',
        [
            'label'       => _m('Image'),
            'description' => _m('The following files are allowed to unload: JPG, JPEG, PNG, GIF. File size should not exceed 100kb. Regardless of the resolution of the source file, it will be converted to the size of the 48х48. For best results, the image should have an equal ratio. New image replaces the old (if it was).')
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
    $error = [];
    if ($_FILES['image']['size'] > 0) {
        require_once ROOT_PATH . 'system/vendor/class.upload/class.upload.php';
        $handle = new upload($_FILES['image']);
        if ($handle->uploaded) {
            $profile = $app->profile();
            // Обрабатываем фото
            $handle->file_new_name_body = $profile->id;
            $handle->allowed =
                [
                    'image/jpeg',
                    'image/gif',
                    'image/png'
                ];
            $handle->file_max_size = Config::$filesize * 1024;
            $handle->file_overwrite = true;
            $handle->image_resize = true;
            $handle->image_x = 48;
            $handle->image_y = 48;
            $handle->image_convert = 'jpg';
            $handle->process(FILES_PATH . 'users' . DS . 'avatar' . DS);
            if ($handle->processed) {
                $profile->avatar = $app->request()->getBaseUrl() . '/uploads/users/avatar/' . $profile->id . '.jpg';
                $profile->save();

                if (is_file(FILES_PATH . 'users' . DS . 'avatar' . DS . $profile->id . '.gif')) {
                    unlink(FILES_PATH . 'users' . DS . 'avatar' . DS . $profile->id . '.gif');
                }

                $form->continueLink = '../';
                $form->successMessage = _m('Avatar is uploaded');
                $form->confirmation = true;
                $app->view()->hideuser = true;
            } else {
                $error[] = ($handle->error);
            }
            $handle->clean();
        }
    } else {
        // Если не выбран файл
        $error[] = _m('The file is not selected');
    }

    if (!empty($error)) {
        $app->view()->error = implode('<br/>', $error);
    }
}

$app->view()->form = $form->display();
$app->view()->setTemplate('edit_form.php');
