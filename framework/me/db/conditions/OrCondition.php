<?php
namespace me\db\conditions;
class OrCondition extends ConjunctionCondition {
    public function getOperator() {
        return 'OR';
    }
}