<?php
namespace app\assets;
use me\components\AssetBundle;
class AdminAsset extends AssetBundle {
    public $web = '@web/assets/default';
    public $css = [
        'css/admin.css',
    ];
    public $js = [
        'js/functions.js',
    ];
    public $depends = [
        'me\assets\JqueryAsset',
        'me\assets\BootstrapAsset',
        'me\assets\MetisMenuAsset',
        'me\assets\Select2Asset',
        'me\assets\FontawesomeAsset',
    ];
}