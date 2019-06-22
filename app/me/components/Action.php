<?php
namespace me\components;
use Me;
use me\helpers\Url;
use me\exceptions\NotFound;
use ReflectionMethod;
class Action extends Component {
    /**
     * @var string
     */
    public $id;
    /**
     * @var Controller
     */
    public $controller;
    /**
     * @var string
     */
    public $actionMethod;
    /**
     * @return bool
     */
    public function checkAccess(): bool {
        if (empty($this->controller->access)) {
            return true;
        }
        /* @var $access Access */
        $access = Me::createObject($this->controller->access);
        return $access->allows($this);
    }
    /**
     * 
     */
    public function run($params) {
        if (!$this->checkAccess()) {
            if (Me::$app->getUser()->getIsGuest()) {
                return $this->controller->redirect([Me::$app->loginRoute]);
            }
            throw new NotFound('Access Denied!');
        }
        $method = new ReflectionMethod($this->controller, $this->actionMethod);
        $args   = [];
        foreach ($method->getParameters() as $param) {
            /* @var $param \ReflectionParameter */
            if (isset($params[$param->name])) {
                $args[$param->name] = $params[$param->name];
            }
            else if ($param->isOptional()) {
                $args[$param->name] = $param->getDefaultValue();
            }
            else {
                throw new NotFound("Parameter {<b>$param->name</b>} Is Missing");
            }
        }
        return call_user_func_array([$this->controller, $this->actionMethod], $args);
    }
}