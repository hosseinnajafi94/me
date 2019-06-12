<?php
namespace me\components;
use me\exceptions\NotFound;
use ReflectionMethod;
class Action extends Component {
    public $id;
    public $controller;
    public $actionMethod;
    public function run($params) {
        $method = new ReflectionMethod($this->controller, $this->actionMethod);
        $args = [];
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