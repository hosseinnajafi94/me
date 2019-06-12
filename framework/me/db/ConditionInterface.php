<?php
namespace me\db;
use me\db\ExpressionInterface;
/**
 * Interface ConditionInterface should be implemented by classes that represent a condition
 * in DBAL of framework.
 */
interface ConditionInterface extends ExpressionInterface {
    public static function fromArrayDefinition($operator, $operands);
}