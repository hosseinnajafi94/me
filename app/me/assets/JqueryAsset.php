<?php
namespace me\assets;
use me\components\AssetBundle;
class JqueryAsset extends AssetBundle {
    public $web = '@web/assets/jquery';
    public $js  = [
        'jquery-3.4.1.min.js',
        'jquery.cookie.js',
    ];
}