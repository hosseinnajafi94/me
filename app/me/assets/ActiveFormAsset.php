<?php
namespace me\assets;
use me\components\AssetBundle;
class ActiveFormAsset extends AssetBundle {
    public $web     = '@web/assets/me';
    public $js      = [
        'me.activeForm.js'
    ];
    public $depends = [
        'me\assets\MeAsset',
    ];
}