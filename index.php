<?php
define('ME_DEBUG', true);
include 'app/me/Me.php';
$config = include 'app/config/web.php';
(new \me\components\Application($config))->run();
