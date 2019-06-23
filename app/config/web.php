<?php
$db = include 'db.php';
list($modules, $translations) = include 'modules.php';
return [
    'modules'      => $modules,
    'translations' => $translations,
    'components'   => [
        'db'         => $db,
        'user'       => [
            'identityClass' => 'app\modules\users\models\DAL\Users',
        ],
        'urlManager' => [
            'map' => [
                ''        => 'site/default/index',
                'signin'  => 'users/auth/signin',
                'signup'  => 'users/auth/signup',
                'signout' => 'users/auth/signout',
            ]
        ],
    ],
];
