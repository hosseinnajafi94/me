<?php
namespace me\assets;
use me\components\AssetBundle;
class MetisMenuAsset extends AssetBundle {
    public $web = '@web/assets/metismenu';
    public $css = [
        'metisMenu.min.css',
        'sb-admin/sb-admin-2.css',
        'sb-admin/sb-admin-2-rtl.css',
    ];
    public $js = [
        'metisMenu.min.js',
    ];
    public $depends = [
        'me\assets\JqueryAsset',
    ];
}