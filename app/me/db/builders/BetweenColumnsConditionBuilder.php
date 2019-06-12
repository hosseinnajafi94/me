<?php
namespace me\db\conditions;
use me\db\ExpressionBuilderInterface;
use me\db\ExpressionBuilderTrait;
use me\db\ExpressionInterface;
use me\db\ActiveQuery;
class BetweenColumnsConditionBuilder implements ExpressionBuilderInterface {
    use ExpressionBuilderTrait;
    public function build(ExpressionInterface $expression, array &$params = []) {
        $operator = $expression->getOperator();
        $startColumn = $this->escapeColumnName($expression->getIntervalStartColumn(), $params);
        $endColumn   = $this->escapeColumnName($expression->getIntervalEndColumn(), $params);
        $value       = $this->createPlaceholder($expression->getValue(), $params);
        return "$value $operator $startColumn AND $endColumn";
    }
    protected function escapeColumnName($columnName, &$params = []) {
        if ($columnName instanceof ActiveQuery) {
            list($sql, $params) = $this->queryBuilder->build($columnName, $params);
            return "($sql)";
        }
        elseif ($columnName instanceof ExpressionInterface) {
            return $this->queryBuilder->buildExpression($columnName, $params);
        }
        elseif (strpos($columnName, '(') === false) {
            return $this->queryBuilder->db->quoteColumnName($columnName);
        }
        return $columnName;
    }
    protected function createPlaceholder($value, &$params) {
        if ($value instanceof ExpressionInterface) {
            return $this->queryBuilder->buildExpression($value, $params);
        }
        return $this->queryBuilder->bindParam($value, $params);
    }
}