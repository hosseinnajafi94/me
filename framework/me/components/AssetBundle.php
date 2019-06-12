<?php
namespace me\components;
class AssetBundle extends Component {
    public $web;
    public $js      = [];
    public $jsText  = [];
    public $cssText = [];
    public $css     = [];
    public $depends = [];
    /**
     * @param Controller $controller
     * @return void
     */
    public static function register(Controller $controller) {
        return $controller->registerAssetBundle(get_called_class());
    }
    public function publish() {
        
    }
}