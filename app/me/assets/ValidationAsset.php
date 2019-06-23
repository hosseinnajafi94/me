<?php
namespace me\assets;
use me\components\AssetBundle;
class ValidationAsset extends AssetBundle {
    public $web     = '@web/assets/me';
    public $js      = [
        'me.validation.js'
    ];
    public $depends = [
        'me\assets\MeAsset',
    ];
}