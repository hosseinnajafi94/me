<?php
namespace me\components;
use Me;
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
        $view = Me::$app->getView();
        $content = $view->renderFile($view->getViewFile(), $items);
        return $view->render($content);
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
        header('location:' . \me\helpers\Url::to($url));
        exit;
    }
}