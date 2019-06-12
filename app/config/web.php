<?php
return [
    'components' => [
        'urlManager' => [
            'map' => [
                ''  => 'site/default/index',
                'login'  => 'users/auth/login',
                'logout' => 'users/auth/logout',
            ]
        ],
        'db'         => include 'db.php',
    ],
    'modules'    => include 'modules.php'
];
