<?php
namespace me\components;
use Me;
use me\helpers\ArrayHelper;
class Access extends Component {
    /**
     * @var array
     */
    public $ruleConfig = ['class' => 'me\components\AccessRule'];
    /**
     * @var AccessRule[]
     */
    public $rules      = [];
    /**
     * 
     */
    public function init() {
        parent::init();
        foreach ($this->rules as $i => $rule) {
            if (is_array($rule)) {
                $this->rules[$i] = Me::createObject(ArrayHelper::Extend($this->ruleConfig, $rule));
            }
        }
    }
    /**
     * @param Action $action Action
     * @return bool
     */
    public function allows(Action $action): bool {
        $user    = Me::$app->getUser();
        $request = Me::$app->getRequest();
        foreach ($this->rules as $rule) {
            if ($rule->allows($action, $user, $request)) {
                return true;
            }
        }
        return false;
    }
}