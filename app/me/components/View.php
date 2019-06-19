<?php
namespace me\components;
use Me;
use me\helpers\Html;
use me\exceptions\NotFound;
use me\components\AssetBundle;
class View extends Component {
    /**
     * @return array
     */
    private $assetBundles = [];
    /**
     * @return array
     */
    private $assets       = [];
    /**
     * @var string
     */
    public $title;
    /**
     * @return string
     */
    private $_layoutFile;
    /**
     * @var string
     */
    private $_viewPath;
    /**
     * @param string $viewname
     * @param array $params
     * @return string
     */
    public function view($viewname, $params = []) {
        return $this->renderFile($this->getViewFile($viewname), $params);
    }
    /**
     * @param string $_file_
     * @param array $_params_
     * @return string
     */
    public function renderFile($_file_, $_params_ = []) {
        if (!is_file($_file_)) {
            throw new NotFound("View File { <b>$_file_</b> } Not Found");
        }
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        require $_file_;
        return ob_get_clean();
    }
    /**
     * @return false|string
     */
    public function getLayoutFile() {
        if ($this->_layoutFile === null) {
            $layout = false;
            if (is_string($this->layout)) {
                $layout = $this->layout;
            }
            else if (is_null($this->layout) && !is_null(Me::$app->layout)) {
                $layout = Me::$app->layout;
            }
            if ($layout === false) {
                $this->_layoutFile = false;
                return false;
            }
            $this->_layoutFile = Me::$app->layoutPath . DIRECTORY_SEPARATOR . $layout . '.php';
            if (!is_file($this->_layoutFile)) {
                throw new NotFound("Layout File { <b>$this->_layoutFile</b> } Not Found");
            }
        }

        return $this->_layoutFile;
    }
    /**
     * @param string $content
     * @return string
     */
    public function render($content) {
        $layoutFile = $this->getLayoutFile();
        if ($layoutFile === false) {
            return $content;
        }
        return $this->renderFile($layoutFile, ['content' => $content]);
    }
    /**
     * @return string
     */
    public function getViewFile($viewFile = null) {
        $viewFile = ($viewFile === null ? Me::$app->module->controller->action->id : $viewFile);
        return $this->getViewPath() . DIRECTORY_SEPARATOR . $viewFile . '.php';
    }
    /**
     * @return string
     */
    public function getViewPath() {
        if ($this->_viewPath === null) {
            $this->_viewPath = Me::$app->module->getViewPath() . DIRECTORY_SEPARATOR . Me::$app->module->controller->id;
        }
        return $this->_viewPath;
    }
    /**
     * @param string $css
     * @return void
     */
    public function registerCss($css, $depends = []) {
        $bundle            = new AssetBundle();
        $bundle->cssText[] = $css;
        foreach ($depends as $dep) {
            $this->registerAssetBundle($dep);
        }
        $this->assets[] = $bundle;
    }
    /**
     * @param string $js
     * @return void
     */
    public function registerJs($js, $depends = []) {
        $bundle           = new AssetBundle();
        $bundle->jsText[] = $js;
        foreach ($depends as $dep) {
            $this->registerAssetBundle($dep);
        }
        $this->assets[] = $bundle;
    }
    /**
     * @param string $name
     * @return void
     */
    public function registerAssetBundle($name) {
        if (isset($this->assetBundles[$name])) {
            //throw new Exceptions("A circular dependency is detected for bundle '$name'.");
            return;
        }
        $this->assetBundles[$name] = false;
        $am                        = Me::$app->getAssetManager();
        $bundle                    = $am->getBundle($name);
        foreach ($bundle->depends as $dep) {
            $this->registerAssetBundle($dep);
        }
        $this->assets[] = $bundle;
    }
    /**
     * @return string
     */
    public function head() {
        $meta   = [];
        $meta[] = '<meta charset="' . Me::$app->charset . '"/>';
        $meta[] = '<meta name="viewport" content="width=device-width, initial-scale=1"/>';
        /* @var $bundle AssetBundle */
        $css    = [];
        foreach ($this->assets as $bundle) {
            foreach ($bundle->css as $href) {
                $css[] = Html::cssLink(Me::getAlias("$bundle->web/$href"));
            }
            foreach ($bundle->cssText as $text) {
                $css[] = Html::style($text);
            }
        }
        return implode("\n        ", $meta)
                . "\n        <title>$this->title</title>"
                . "\n        " . implode("\n        ", $css)
                . "\n";
    }
    /**
     * @return string
     */
    public function body() {
        /* @var $bundle AssetBundle */
        $js = [];
        foreach ($this->assets as $bundle) {
            foreach ($bundle->js as $src) {
                $js[] = Html::scriptLink(Me::getAlias("$bundle->web/$src"));
            }
            foreach ($bundle->jsText as $text) {
                $js[] = Html::script($text);
            }
        }
        return "\n        " . implode("\n        ", $js) . "\n";
    }
}