<?php
namespace me\db\conditions;
use me\db\ExpressionBuilderInterface;
use me\db\ExpressionBuilderTrait;
use me\db\ExpressionInterface;
class NotConditionBuilder implements ExpressionBuilderInterface {
    use ExpressionBuilderTrait;
    public function build(ExpressionInterface $expression, array &$params = []) {
        $operand = $expression->getCondition();
        if ($operand === '') {
            return '';
        }

        $expession = $this->queryBuilder->buildCondition($operand, $params);
        return "{$this->getNegationOperator()} ($expession)";
    }
    protected function getNegationOperator() {
        return 'NOT';
    }
}