<?php
return [
    'components' => [
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
        'db'         => include 'db.php',
    ],
    'modules'    => include 'modules.php'
];
