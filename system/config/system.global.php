<?php
return [
    'mobicms' => [
        'antifloodMode'              => 1,
        'antifloodDayDelay'          => 10,
        'antifloodNightDelay'        => 40,
        'copyright'                  => 'Powered by mobiCMS',
        'email'                      => 'user@example.com',
        'filesize'                   => 2100,
        'guestsAllowOnlineLists'     => true,
        'guestsAllowUserLists'       => true,
        'guestsAllowViewProfiles'    => true,
        'homeTitle'                  => 'mobiCMS!',
        'homeUrl'                    => 'http://mobicms/mobicms-archive',
        'lng'                        => 'ru',
        'lngSwitch'                  => true,
        'metaDesc'                   => 'mobiCMS mobile content management system http://mobicms.net',
        'metaKey'                    => 'mobicms',
        'profilingGeneration'        => true,
        'profilingMemory'            => true,
        'quarantine'                 => [
            'period'         => 24,
            'mailSent'       => 5,
            'mailRecipients' => 3,
            'comments'       => 5,
            'uploadImages'   => 3,
            'reputation'     => false,
            'album'          => 3,
        ],
        'registrationAllow'          => true,
        'registrationApproveByAdmin' => false,
        'registrationLetterMode'     => 2,
        'registrationQuarantine'     => false,
        'siteName'                   => 'mobiCMS',
        'timeshift'                  => 4,
        'userAllowChangeNickname'    => true,
        'userAllowChangeSex'         => false,
        'userAllowChangeStatus'      => true,
        'userAllowNicknamesOfDigits' => false,
        'userAllowUploadAvatars'     => true,
        'userAllowUseGravatar'       => true,
        'userChangeNicknamePeriod'   => 30,
    ],
];
