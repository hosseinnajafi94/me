<?php
namespace me\db\conditions;
use me\db\ExpressionBuilderInterface;
use me\db\ExpressionBuilderTrait;
use me\db\ExpressionInterface;
class ConjunctionConditionBuilder implements ExpressionBuilderInterface {
    use ExpressionBuilderTrait;
    public function build(ExpressionInterface $condition, array &$params = []) {
        $parts = $this->buildExpressionsFrom($condition, $params);
        if (empty($parts)) {
            return '';
        }
        if (count($parts) === 1) {
            return reset($parts);
        }
        return '(' . implode(") {$condition->getOperator()} (", $parts) . ')';
    }
    private function buildExpressionsFrom(ExpressionInterface $condition, &$params = []) {
        $parts = [];
        foreach ($condition->getExpressions() as $condition) {
            if (is_array($condition)) {
                $condition = $this->queryBuilder->buildCondition($condition, $params);
            }
            if ($condition instanceof ExpressionInterface) {
                $condition = $this->queryBuilder->buildExpression($condition, $params);
            }
            if ($condition !== '') {
                $parts[] = $condition;
            }
        }

        return $parts;
    }
}