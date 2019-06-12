<?php
namespace me\db\conditions;
class BetweenCondition implements ConditionInterface {
    private $operator;
    private $column;
    private $intervalStart;
    private $intervalEnd;
    public function __construct($column, $operator, $intervalStart, $intervalEnd) {
        $this->column        = $column;
        $this->operator      = $operator;
        $this->intervalStart = $intervalStart;
        $this->intervalEnd   = $intervalEnd;
    }
    public function getOperator() {
        return $this->operator;
    }
    public function getColumn() {
        return $this->column;
    }
    public function getIntervalStart() {
        return $this->intervalStart;
    }
    public function getIntervalEnd() {
        return $this->intervalEnd;
    }
    public static function fromArrayDefinition($operator, $operands) {
        return new static($operands[0], $operator, $operands[1], $operands[2]);
    }
}