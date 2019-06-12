<?php
namespace app\assets;
class LoginAsset extends AssetBundle {
    public $web = '@web/assets/default';
    public $css = [
        'css/login.css',
    ];
    public $js = [
        'js/functions.js',
    ];
    public $depends = [
        'me\assets\JqueryAsset',
        'me\assets\BootstrapAsset',
        'me\assets\FontawesomeAsset',
    ];
}