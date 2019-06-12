<?php
namespace yii\db\conditions;
use yii\base\InvalidArgumentException;
class SimpleCondition implements ConditionInterface {
    private $operator;
    private $column;
    private $value;
    public function __construct($column, $operator, $value) {
        $this->column   = $column;
        $this->operator = $operator;
        $this->value    = $value;
    }
    public function getOperator() {
        return $this->operator;
    }
    public function getColumn() {
        return $this->column;
    }
    public function getValue() {
        return $this->value;
    }
    public static function fromArrayDefinition($operator, $operands) {
        return new static($operands[0], $operator, $operands[1]);
    }
}