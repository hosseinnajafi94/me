<?php
namespace me\components;
use Me;
use me\helpers\Url;
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
    private $_layoutFile;
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
        $view    = Me::$app->getView();
        $content = $view->renderFile($view->getViewFile(), $items);
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
     * 
     */
    public function redirect($url = []) {
        header('location:' . Url::to($url));
        exit;
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
        $view = Me::$app->getView();
        return $view->renderFile($layoutFile, ['content' => $content]);
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
}