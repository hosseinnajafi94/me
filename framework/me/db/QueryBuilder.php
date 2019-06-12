<?php
namespace me\db;
use me\components\Component;
class QueryBuilder extends Component {
    /**
     * @var Db
     */
    public $db;
    /**
     * @var string
     */
    public $separator = ' ';
    /**
     * @param ActiveQuery $query
     * @param array $params
     * @return array
     */
    public function build(ActiveQuery $query, array &$params = []) {
        
    }
    /**
     * @param string $table
     * @param array|string $condition
     * @param array $params
     * @return string
     */
    public function delete(string $table, $condition = '', array &$params = []) {
        
    }
    /**
     * @param string $table
     * @param array $columns
     * @param array $params
     * @return string
     */
    public function insert(string $table, array $columns = [], array &$params = []) {
        
    }
    /**
     * @param string $table
     * @param array $columns
     * @param array|string $condition
     * @param array $params
     * @return string
     */
    public function update(string $table, array $columns = [], $condition = '', array &$params = []) {
        
    }
    /**
     * @var array
     */
    public $conditionClasses   = [];
    /**
     * @var array
     */
    public $expressionBuilders = [];
    /**
     * {@inheritdoc}
     */
    public function init() {
        parent::init();
        $this->conditionClasses   = array_merge($this->defaultConditionClasses(), $this->conditionClasses);
        $this->expressionBuilders = array_merge($this->defaultExpressionBuilders(), $this->expressionBuilders);
    }
    /**
     * @return array
     */
    public function defaultConditionClasses() {
        return [
            'AND'         => 'me\db\conditions\AndCondition',
            'BETWEEN'     => 'me\db\conditions\BetweenCondition',
            'NOT BETWEEN' => 'me\db\conditions\BetweenCondition',
            'EXISTS'      => 'me\db\conditions\ExistsCondition',
            'NOT EXISTS'  => 'me\db\conditions\ExistsCondition',
            'IN'          => 'me\db\conditions\InCondition',
            'NOT IN'      => 'me\db\conditions\InCondition',
            'LIKE'        => 'me\db\conditions\LikeCondition',
            'NOT LIKE'    => 'me\db\conditions\LikeCondition',
            'OR LIKE'     => 'me\db\conditions\LikeCondition',
            'OR NOT LIKE' => 'me\db\conditions\LikeCondition',
            'NOT'         => 'me\db\conditions\NotCondition',
            'OR'          => 'me\db\conditions\OrCondition',
        ];
    }
    /**
     * @return array
     */
    public function defaultExpressionBuilders() {
        return [
            'me\db\ActiveQuery'                        => 'me\db\QueryExpressionBuilder',
            'me\db\PdoValue'                           => 'me\db\PdoValueBuilder',
            'me\db\Expression'                         => 'me\db\ExpressionBuilder',
            'me\db\conditions\BetweenColumnsCondition' => 'me\db\builders\BetweenColumnsConditionBuilder',
            'me\db\conditions\BetweenCondition'        => 'me\db\builders\BetweenConditionBuilder',
            'me\db\conditions\ConjunctionCondition'    => 'me\db\builders\ConjunctionConditionBuilder',
            'me\db\conditions\AndCondition'            => 'me\db\builders\ConjunctionConditionBuilder',
            'me\db\conditions\OrCondition'             => 'me\db\builders\ConjunctionConditionBuilder',
            'me\db\conditions\ExistsCondition'         => 'me\db\builders\ExistsConditionBuilder',
            'me\db\conditions\HashCondition'           => 'me\db\builders\HashConditionBuilder',
            'me\db\conditions\InCondition'             => 'me\db\builders\InConditionBuilder',
            'me\db\conditions\LikeCondition'           => 'me\db\builders\LikeConditionBuilder',
            'me\db\conditions\NotCondition'            => 'me\db\builders\NotConditionBuilder',
            'me\db\conditions\SimpleCondition'         => 'me\db\builders\SimpleConditionBuilder',
        ];
    }
}