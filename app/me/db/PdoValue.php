<?php
namespace me\db;
final class PdoValue implements ExpressionInterface {
    private $value;
    private $type;
    public function __construct($value, $type) {
        $this->value = $value;
        $this->type  = $type;
    }
    public function getValue() {
        return $this->value;
    }
    public function getType() {
        return $this->type;
    }
}