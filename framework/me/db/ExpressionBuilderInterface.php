<?php
namespace me\db;
/**
 * Interface ExpressionBuilderInterface is designed to build raw SQL from specific expression
 * objects that implement [[ExpressionInterface]].
 */
interface ExpressionBuilderInterface {
    public function build(ExpressionInterface $expression, array &$params = []);
}