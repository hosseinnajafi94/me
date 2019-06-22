<?php
namespace app\assets;
use me\components\AssetBundle;
class SiteAsset extends AssetBundle {
    public $web = '@web/assets/default';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        // 'js/functions.js',
    ];
    public $depends = [
        'me\assets\JqueryAsset',
        'me\assets\BootstrapAsset',
        'me\assets\FontawesomeAsset',
    ];
}