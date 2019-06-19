<?php
return [
    'components' => [
        'urlManager' => [
            'map' => [
                ''  => 'site/default/index',
                'signin'  => 'users/auth/signin',
                'signout' => 'users/auth/signout',
            ]
        ],
        'db'         => include 'db.php',
    ],
    'modules'    => include 'modules.php'
];
