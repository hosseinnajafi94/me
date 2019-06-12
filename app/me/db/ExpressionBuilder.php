<?php
namespace me\db;
class ExpressionBuilder implements ExpressionBuilderInterface {
    use ExpressionBuilderTrait;
    public function build(ExpressionInterface $expression, array &$params = []) {
        $params = array_merge($params, $expression->params);
        return $expression->__toString();
    }
}