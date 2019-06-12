<?php
namespace me\db\conditions;
use me\db\ExpressionBuilderInterface;
use me\db\ExpressionBuilderTrait;
use me\db\ExpressionInterface;
class LikeConditionBuilder implements ExpressionBuilderInterface {
    use ExpressionBuilderTrait;
    protected $escapingReplacements = [
        '%'  => '\%',
        '_'  => '\_',
        '\\' => '\\\\',
    ];
    protected $escapeCharacter;
    public function build(ExpressionInterface $expression, array &$params = []) {
        $operator = $expression->getOperator();
        $column   = $expression->getColumn();
        $values   = $expression->getValue();
        $escape   = $expression->getEscapingReplacements();
        if ($escape === null || $escape === []) {
            $escape = $this->escapingReplacements;
        }
        list($andor, $not, $operator) = $this->parseOperator($operator);
        if (!is_array($values)) {
            $values = [$values];
        }
        if (empty($values)) {
            return $not ? '' : '0=1';
        }
        if (strpos($column, '(') === false) {
            $column = $this->queryBuilder->db->quoteColumnName($column);
        }
        $escapeSql = $this->getEscapeSql();
        $parts     = [];
        foreach ($values as $value) {
            if ($value instanceof ExpressionInterface) {
                $phName = $this->queryBuilder->buildExpression($value, $params);
            }
            else {
                $phName = $this->queryBuilder->bindParam(empty($escape) ? $value : ('%' . strtr($value, $escape) . '%'), $params);
            }
            $parts[] = "{$column} {$operator} {$phName}{$escapeSql}";
        }
        return implode($andor, $parts);
    }
    private function getEscapeSql() {
        if ($this->escapeCharacter !== null) {
            return " ESCAPE '{$this->escapeCharacter}'";
        }
        return '';
    }
    protected function parseOperator($operator) {
        $matches = [];
        preg_match('/^(AND |OR |)(((NOT |))I?LIKE)/', $operator, $matches);
        $andor    = ' ' . (!empty($matches[1]) ? $matches[1] : 'AND ');
        $not      = !empty($matches[3]);
        $operator = $matches[2];
        return [$andor, $not, $operator];
    }
}