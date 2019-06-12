<?php
namespace me\db;
class Expression extends me\components\Component implements ExpressionInterface {
    public $expression;
    public $params = [];
    public function __construct($expression, $params = [], $config = []) {
        $this->expression = $expression;
        $this->params     = $params;
        parent::__construct($config);
    }
    public function __toString() {
        return $this->expression;
    }
}