<?php
namespace me\db\conditions;
use me\db\ConditionInterface;
/**
 * Condition based on column-value pairs.
 */
class HashCondition implements ConditionInterface {
    /**
     * @var array|null the condition specification.
     */
    private $hash;
    /**
     * HashCondition constructor.
     *
     * @param array|null $hash
     */
    public function __construct($hash) {
        $this->hash = $hash;
    }
    /**
     * @return array|null
     */
    public function getHash() {
        return $this->hash;
    }
    /**
     * {@inheritdoc}
     */
    public static function fromArrayDefinition($operator, $operands) {
        return new static($operands);
    }
}