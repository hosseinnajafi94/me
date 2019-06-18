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
     * @param View $view
     * @return void
     */
    public static function register(View $view) {
        return $view->registerAssetBundle(get_called_class());
    }
    public function publish() {
        
    }
}