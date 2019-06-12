<?php
namespace me\db\conditions;
use me\db\ExpressionInterface;
use me\db\ConditionInterface;
class InCondition implements ConditionInterface {
    private $operator;
    private $column;
    private $values;
    public function __construct($column, $operator, $values) {
        $this->column   = $column;
        $this->operator = $operator;
        $this->values   = $values;
    }
    public function getOperator() {
        return $this->operator;
    }
    public function getColumn() {
        return $this->column;
    }
    public function getValues() {
        return $this->values;
    }
    public static function fromArrayDefinition($operator, $operands) {
        return new static($operands[0], $operator, $operands[1]);
    }
}