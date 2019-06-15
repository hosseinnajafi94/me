<?php
namespace me\components;
use Me;
use me\helpers\Html;
use me\exceptions\NotFound;
use ReflectionMethod;
/**
 * 
 */
class Controller extends Component {
    /**
     * @var array
     */
    public $access = [];
    /**
     * @var string
     */
    public $id;
    /**
     * @var Module
     */
    public $module;
    /**
     * @var Action
     */
    public $action;
    /**
     * @var string
     */
    public $layout;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    private $_viewPath;
    /**
     * @return string
     */
    private $_viewFile;
    /**
     * @return string
     */
    private $_layoutFile;
    /**
     * @return array
     */
    private $assetBundles = [];
    /**
     * @return array
     */
    private $assets       = [];
    /**
     * @param string $actionID
     * @param array $params
     * @return Response|string
     */
    public function runAction(string $actionID, array $params = []) {
        /* @var $action Action */
        $action       = $this->createAction($actionID);
        $this->action = $action;
        $result       = $action->run($params);
        return $result;
    }
    /**
     * @param string $actionID
     * @return Action
     */
    public function createAction(string $actionID) {
        $methodName = str_replace(' ', '', ucwords(str_replace('-', ' ', $actionID)));
        if (method_exists($this, $methodName)) {
            $method = new ReflectionMethod($this, $methodName);
            if ($method->isPublic()) {
                return Me::createObject([
                    'class'        => Action::class,
                    'id'           => $actionID,
                    'controller'   => $this,
                    'actionMethod' => $methodName
                ]);
            }
        }
        throw new NotFound("Action { <b>$actionID</b> } Not Found");
    }
    /**
     * @param mixed $model
     * @return string
     */
    public function view(array $items = []) {
        $content = $this->renderFile($this->getViewFile(), $items);
        return $this->render($content);
    }
    /**
     * @param array|object $params
     * @return Response
     */
    public function json($params = []) {
        $response         = Me::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data   = json_encode($params);
        return $response;
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
    /**
     * @param string $content
     * @return string
     */
    private function render($content) {
        $layoutFile = $this->getLayoutFile();
        if ($layoutFile === false) {
            return $content;
        }
        return $this->renderFile($layoutFile, ['content' => $content]);
    }
    /**
     * @param string $_file_
     * @param array $_params_
     * @return string
     */
    private function renderFile($_file_, $_params_ = []) {
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
     * @return string
     */
    private function getViewPath() {
        if ($this->_viewPath === null) {
            $this->_viewPath = $this->module->getViewPath() . DIRECTORY_SEPARATOR . $this->id;
        }
        return $this->_viewPath;
    }
    /**
     * @return string
     */
    private function getViewFile() {
        if ($this->_viewFile === null) {
            $this->_viewFile = $this->getViewPath() . DIRECTORY_SEPARATOR . $this->action->id . '.php';
        }
        return $this->_viewFile;
    }
    /**
     * @return false|string
     */
    private function getLayoutFile() {
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
    public function redirect($url = []) {
        header('location:' . \me\helpers\Url::to($url));
        exit;
    }
}