<?php
namespace me\db\builders;
use me\db\ExpressionBuilderInterface;
use me\db\ExpressionBuilderTrait;
use me\db\ExpressionInterface;
use me\db\ConditionInterface;
use me\db\ActiveQuery;
class InConditionBuilder implements ExpressionBuilderInterface {
    use ExpressionBuilderTrait;
    public function build(ExpressionInterface $expression, array &$params = []) {
        $operator = $expression->getOperator();
        $column   = $expression->getColumn();
        $values   = $expression->getValues();
        if ($column === []) {
            return $operator === 'IN' ? '0=1' : '';
        }
        if ($values instanceof ActiveQuery) {
            return $this->buildSubqueryInCondition($operator, $column, $values, $params);
        }
        if (!is_array($values) && !$values instanceof \Traversable) {
            $values = (array) $values;
        }
        if (is_array($column)) {
            if (count($column) > 1) {
                return $this->buildCompositeInCondition($operator, $column, $values, $params);
            }
            else {
                $column = reset($column);
            }
        }
        if ($column instanceof \Traversable) {
            if (iterator_count($column) > 1) {
                return $this->buildCompositeInCondition($operator, $column, $values, $params);
            }
            else {
                $column->rewind();
                $column = $column->current();
            }
        }
        $sqlValues = $this->buildValues($expression, $values, $params);
        if (empty($sqlValues)) {
            return $operator === 'IN' ? '0=1' : '';
        }
        if (count($sqlValues) > 1) {
            return "$column $operator (" . implode(', ', $sqlValues) . ')';
        }
        $operator = $operator === 'IN' ? '=' : '<>';
        return $column . $operator . reset($sqlValues);
    }
    protected function buildValues(ConditionInterface $condition, $values, &$params) {
        $sqlValues = [];
        $column    = $condition->getColumn();
        if (is_array($column)) {
            $column = reset($column);
        }
        if ($column instanceof \Traversable) {
            $column->rewind();
            $column = $column->current();
        }
        foreach ($values as $i => $value) {
            if (is_array($value) || $value instanceof \ArrayAccess) {
                $value = isset($value[$column]) ? $value[$column] : null;
            }
            if ($value === null) {
                $sqlValues[$i] = 'NULL';
            }
            elseif ($value instanceof ExpressionInterface) {
                $sqlValues[$i] = $this->queryBuilder->buildExpression($value, $params);
            }
            else {
                $sqlValues[$i] = $this->queryBuilder->bindParam($value, $params);
            }
        }
        return $sqlValues;
    }
    protected function buildSubqueryInCondition($operator, $columns, $values, &$params) {
        $sql = $this->queryBuilder->buildExpression($values, $params);
        if (is_array($columns)) {
            foreach ($columns as $i => $col) {
                if (strpos($col, '(') === false) {
                    $columns[$i] = $col;
                }
            }
            return '(' . implode(', ', $columns) . ") $operator $sql";
        }
        return "$columns $operator $sql";
    }
    protected function buildCompositeInCondition($operator, $columns, $values, &$params) {
        $vss = [];
        foreach ($values as $value) {
            $vs = [];
            foreach ($columns as $column) {
                if (isset($value[$column])) {
                    $vs[] = $this->queryBuilder->bindParam($value[$column], $params);
                }
                else {
                    $vs[] = 'NULL';
                }
            }
            $vss[] = '(' . implode(', ', $vs) . ')';
        }
        if (empty($vss)) {
            return $operator === 'IN' ? '0=1' : '';
        }
        $sqlColumns = [];
        foreach ($columns as $i => $column) {
            $sqlColumns[] = $column;
        }
        return '(' . implode(', ', $sqlColumns) . ") $operator (" . implode(', ', $vss) . ')';
    }
}