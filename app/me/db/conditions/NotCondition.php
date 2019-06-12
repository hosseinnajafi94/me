<?php
namespace me\db\conditions;
use me\db\ConditionInterface;
class NotCondition implements ConditionInterface {
    private $condition;
    public function __construct($condition) {
        $this->condition = $condition;
    }
    public function getCondition() {
        return $this->condition;
    }
    public static function fromArrayDefinition($operator, $operands) {
        if (count($operands) !== 1) {
            throw new InvalidArgumentException("Operator '$operator' requires exactly one operand.");
        }

        return new static(array_shift($operands));
    }
}