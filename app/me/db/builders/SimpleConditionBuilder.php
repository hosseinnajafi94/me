<?php
namespace me\db\builders;
use me\db\ExpressionBuilderInterface;
use me\db\ExpressionBuilderTrait;
use me\db\ExpressionInterface;
class SimpleConditionBuilder implements ExpressionBuilderInterface {
    use ExpressionBuilderTrait;
    public function build(ExpressionInterface $expression, array &$params = []) {
        $operator = $expression->getOperator();
        $column   = $expression->getColumn();
        $value    = $expression->getValue();
        if ($value === null) {
            return "$column $operator NULL";
        }
        if ($value instanceof ExpressionInterface) {
            return "$column $operator {$this->queryBuilder->buildExpression($value, $params)}";
        }

        $phName = $this->queryBuilder->bindParam($value, $params);
        return "$column $operator $phName";
    }
}