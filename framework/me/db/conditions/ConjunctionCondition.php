<?php
namespace me\db\conditions;
use me\db\ConditionInterface;
abstract class ConjunctionCondition implements ConditionInterface {
    protected $expressions;
    public function __construct($expressions) { // TODO: use variadic params when PHP>5.6
        $this->expressions = $expressions;
    }
    public function getExpressions() {
        return $this->expressions;
    }
    abstract public function getOperator();
    public static function fromArrayDefinition($operator, $operands) {
        return new static($operands);
    }
}