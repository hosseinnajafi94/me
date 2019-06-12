<?php
namespace me\db;
class QueryExpressionBuilder implements ExpressionBuilderInterface {
    use ExpressionBuilderTrait;
    public function build(ExpressionInterface $expression, array &$params = []) {
        list($sql, $params) = $this->queryBuilder->build($expression, $params);
        return "($sql)";
    }
}