<?php
namespace me\components;
class JsExpression extends Component {
    public $expression;
    public function __construct($expression, $config = []) {
        $this->expression = $expression;
        parent::__construct($config);
    }
}