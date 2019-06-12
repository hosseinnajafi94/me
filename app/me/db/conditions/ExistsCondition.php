<?php
namespace me\db\conditions;
class ExistsCondition implements ConditionInterface {
    private $operator;
    private $query;
    public function __construct($operator, $query) {
        $this->operator = $operator;
        $this->query    = $query;
    }
    public static function fromArrayDefinition($operator, $operands) {
        return new static($operator, $operands[0]);
    }
    public function getOperator() {
        return $this->operator;
    }
    public function getQuery() {
        return $this->query;
    }
}