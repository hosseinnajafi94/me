<?php
namespace me\db\mysql;
use me\db\ActiveQuery;
use me\db\ExpressionInterface;
use me\db\conditions\HashCondition;
class QueryBuilder extends \me\db\QueryBuilder {
    /**
     * @var string
     */
    const PARAM_PREFIX          = ':qp';
    /**
     * @param ActiveQuery $query
     * @return string
     */
    public function build(ActiveQuery $query, array &$params = []) {
        $query   = $query->prepare($this);
        $clauses = [
            $this->buildSelect($query->select),
            $this->buildFrom($query->from),
            $this->buildJoin($query->join),
            $this->buildWhere($query->where, $params),
            $this->buildGroupBy($query->groupBy),
        ];
        $sql     = implode($this->separator, array_filter($clauses));
        $sql     = $this->buildOrderByAndLimit($sql, $query->orderBy, $query->limit, $query->offset);
        return $sql;
    }
    /**
     * @param string $table
     * @param string|array $condition
     * @param array $params
     * @return string
     */
    public function delete(string $table, $condition = '', array &$params = []) {
        $sql   = "DELETE FROM `$table`";
        $where = $this->buildWhere($condition, $params);
        return $where === '' ? $sql : $sql . ' ' . $where;
    }
    /**
     * @param string $table
     * @param array $columns
     * @param array $params
     * @return string
     */
    public function insert(string $table, array $columns = [], array &$params = []) {
        list($names, $placeholders, $params) = $this->prepareInsertValues($columns, $params);
        return 'INSERT INTO ' . $table
                . (!empty($names) ? ' (' . implode(', ', $names) . ')' : '')
                . (!empty($placeholders) ? ' VALUES (' . implode(', ', $placeholders) . ')' : ' DEFAULT VALUES');
    }
    /**
     * @param string $table
     * @param array $columns
     * @param array|string $condition
     * @param array $params
     * @return string
     */
    public function update(string $table, array $columns = [], $condition = '', array &$params = []) {
        list($lines, $params) = $this->prepareUpdateSets($columns, $params);
        $sql   = 'UPDATE ' . $table . ' SET ' . implode(', ', $lines);
        $where = $this->buildWhere($condition, $params);
        return $where === '' ? $sql : $sql . ' ' . $where;
    }
    /**
     * @param mixed $value
     * @param array $params
     * @return string
     */
    public function bindParam($value, array &$params = []) {
        $phName          = static::PARAM_PREFIX . count($params);
        $params[$phName] = $value;
        return $phName;
    }
    //--------------------------------------------------------------------------
    /**
     * @param string|array $condition
     * @param array $params
     * @return string
     */
    public function buildWhere($condition, array &$params = []) {
        $where = $this->buildCondition($condition, $params);
        return $where === '' ? '' : 'WHERE ' . $where;
    }
    /**
     * @param string|array $condition
     * @param array $params
     * @return string
     */
    public function buildCondition($condition, array &$params = []) {
        if (is_array($condition)) {
            if (empty($condition)) {
                return '';
            }
            $condition = $this->createConditionFromArray($condition);
        }
        if ($condition instanceof ExpressionInterface) {
            return $this->buildExpression($condition, $params);
        }
        return (string) $condition;
    }
    /**
     * @param array $condition
     * @param array $params
     * @return string
     */
    public function createConditionFromArray(array $condition = []) {
        if (isset($condition[0])) { // operator format: operator, operand 1, operand 2, ...
            $operator = strtoupper(array_shift($condition));
            if (isset($this->conditionClasses[$operator])) {
                $className = $this->conditionClasses[$operator];
            }
            else {
                $className = 'me\db\conditions\SimpleCondition';
            }
            /* @var $className \me\db\ConditionInterface */
            return $className::fromArrayDefinition($operator, $condition);
        }
        return new HashCondition($condition);
    }
    /**
     * @param ExpressionInterface $expression
     * @param array $params
     */
    public function buildExpression(ExpressionInterface $expression, array &$params = []) {
        $builder = $this->getExpressionBuilder($expression);
        return $builder->build($expression, $params);
    }
    /**
     * @param ExpressionInterface $expression
     * @return mixed
     */
    public function getExpressionBuilder(ExpressionInterface $expression) {
        $className = get_class($expression);
        if (!isset($this->expressionBuilders[$className])) {
            foreach (array_reverse($this->expressionBuilders) as $expressionClass => $builderClass) {
                if (is_subclass_of($expression, $expressionClass)) {
                    $this->expressionBuilders[$className] = $builderClass;
                    break;
                }
            }
            if (!isset($this->expressionBuilders[$className])) {
                return false;
            }
        }
        if ($this->expressionBuilders[$className] === __CLASS__) {
            return $this;
        }
        if (!is_object($this->expressionBuilders[$className])) {
            $this->expressionBuilders[$className] = new $this->expressionBuilders[$className]($this);
        }
        return $this->expressionBuilders[$className];
    }
    //--------------------------------------------------------------------------
    /**
     * @param array $columns
     * @return string
     */
    protected function buildSelect(array $columns = []) {
        $select = 'SELECT';
        if (empty($columns)) {
            return $select . ' *';
        }
        return $select . ' ' . implode(', ', $columns);
    }
    /**
     * @param array $tables
     * @return string
     */
    protected function buildFrom(array $tables = []) {
        if (empty($tables)) {
            return '';
        }
        return 'FROM ' . implode(', ', $tables);
    }
    /**
     * @param array $joins
     * @return string
     */
    protected function buildJoin(array $joins = []) {
        if (empty($joins)) {
            return '';
        }
        foreach ($joins as $i => $join) {
            // 0:join type, 1:join table, 2:on-condition (optional)
            list($type, $table) = $join;
            $joins[$i] = "$type $table";
            if (isset($join[2])) {
                $joins[$i] .= ' ON ' . $join[2];
            }
        }
        return implode($this->separator, $joins);
    }
    /**
     * @param array $columns
     * @return string
     */
    protected function buildGroupBy(array $columns = []) {
        if (empty($columns)) {
            return '';
        }
        return 'GROUP BY ' . implode(', ', $columns);
    }
    /**
     * @param string $sql
     * @param array $orderBy
     * @param int|string $limit
     * @param int|string $offset
     * @return string
     */
    protected function buildOrderByAndLimit(string $sql, array $orderBy, string $limit, string $offset) {
        $orderBy = $this->buildOrderBy($orderBy);
        if ($orderBy !== '') {
            $sql .= $this->separator . $orderBy;
        }
        $limit = $this->buildLimit($limit, $offset);
        if ($limit !== '') {
            $sql .= $this->separator . $limit;
        }
        return $sql;
    }
    /**
     * @param array $columns
     * @return string
     */
    protected function buildOrderBy(array $columns = []) {
        if (empty($columns)) {
            return '';
        }
        $orders = [];
        foreach ($columns as $name => $direction) {
            $orders[] = $name . ($direction === SORT_DESC ? ' DESC' : ' ASC');
        }
        return 'ORDER BY ' . implode(', ', $orders);
    }
    /**
     * @param int|string $limit
     * @param int|string $offset
     * @return string
     */
    protected function buildLimit(string $limit, string $offset) {
        $sql = '';
        if ($this->hasLimit($limit)) {
            $sql = 'LIMIT ' . $limit;
            if ($this->hasOffset($offset)) {
                $sql .= ' OFFSET ' . $offset;
            }
        }
        return ltrim($sql);
    }
    /**
     * @param int|string $limit
     * @return bool
     */
    protected function hasLimit(string $limit) {
        return ctype_digit($limit);
    }
    /**
     * @param int|string $offset
     * @return bool
     */
    protected function hasOffset(string $offset) {
        return ctype_digit($offset) && $offset !== '0';
    }
    /**
     * @param array $columns
     * @param array $params
     * @return array [$names, $placeholders, $params]
     */
    protected function prepareInsertValues(array $columns = [], array $params = []) {
        $names        = [];
        $placeholders = [];
        foreach ($columns as $name => $value) {
            $names[]        = $name;
            $placeholders[] = $this->bindParam($value, $params);
        }
        return [$names, $placeholders, $params];
    }
    /**
     * @param array $columns
     * @param array $params
     * @return array [$sets, $params]
     */
    protected function prepareUpdateSets(array $columns = [], array $params = []) {
        $sets = [];
        foreach ($columns as $name => $value) {
            $placeholder = $this->bindParam($value, $params);
            $sets[]      = $name . '=' . $placeholder;
        }
        return [$sets, $params];
    }
}