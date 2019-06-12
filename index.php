<?php
define('ME_DEBUG', true);
include 'framework/me/Me.php';
$config = include 'framework/config/web.php';
(new \me\components\Application($config))->run();
