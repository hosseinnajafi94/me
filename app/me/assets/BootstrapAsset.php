<?php
namespace me\assets;
use me\components\AssetBundle;
class BootstrapAsset extends AssetBundle {
    public $web     = '@web/assets/bootstrap-3.3.7-dist';
    public $css     = [
        'css/bootstrap.min.css',
        'css/bootstrap-theme.min.css',
        'css/bootstrap-rtl.min.css',
    ];
    public $js      = [
        'js/bootstrap.min.js'
    ];
    public $depends = [
        'me\assets\JqueryAsset',
    ];
}