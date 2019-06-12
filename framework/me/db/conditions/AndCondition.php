<?php
namespace me\db\conditions;
/**
 * Condition that connects two or more SQL expressions with the `AND` operator.
 */
class AndCondition extends ConjunctionCondition {
    /**
     * Returns the operator that is represented by this condition class, e.g. `AND`, `OR`.
     *
     * @return string
     */
    public function getOperator() {
        return 'AND';
    }
}