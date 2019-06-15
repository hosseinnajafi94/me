<?php
echo '<pre dir="ltr">';
session_start();
var_dump(Me::$app->user->isGuest);
var_dump($_SESSION);
var_dump($_COOKIE);
echo '</pre>';