<?php
namespace me\assets;
use me\components\AssetBundle;
class MeAsset extends AssetBundle {
    public $web     = '@web/assets/me';
    public $js      = [
        'me.js'
    ];
    public $depends = [
        'me\assets\JqueryAsset',
        'me\assets\BootstrapAsset',
    ];
}