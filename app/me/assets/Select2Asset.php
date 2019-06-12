<?php
namespace me\assets;
use me\components\AssetBundle;
class Select2Asset extends AssetBundle {
    public $web = '@web/assets/select2';
    public $css = [
        'css/select2.min.css',
    ];
    public $js = [
        'js/select2.full.min.js',
        'js/i18n/fa.js',
    ];
    public $depends = [
        'me\assets\BootstrapAsset',
    ];
}