<?php
namespace me\db;
class PdoValueBuilder implements ExpressionBuilderInterface {
    const PARAM_PREFIX = ':pv';
    public function build(ExpressionInterface $expression, array &$params = []) {
        $placeholder          = static::PARAM_PREFIX . count($params);
        $params[$placeholder] = $expression;
        return $placeholder;
    }
}