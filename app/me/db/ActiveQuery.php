<?php
namespace me\db;
use Me;
use me\components\Component;
class ActiveQuery extends Component {
    /**
     * @var string
     */
    public $modelClass;
    /**
     * @var string
     */
    public $sql;
    /**
     * @var Db
     */
    public $db;
    /**
     * @var array
     */
    public $select;
    /**
     * @var array
     */
    public $from;
    /**
     * @var array|string
     */
    public $where;
    /**
     * @var array
     */
    public $groupBy;
    /**
     * @var array
     */
    public $orderBy;
    /**
     * @var array
     */
    public $join;
    /**
     * @var int|string
     */
    public $limit;
    /**
     * @var int|string
     */
    public $offset;
    /**
     * @var string
     */
    public $indexBy;
    /**
     * @return ActiveRecord
     */
    public function one() {
        $row = $this->createCommand()->queryOne();
        if ($row === false || $row === null) {
            return null;
        }
        $models = $this->populate([$row]);
        return reset($models) ?: null;
    }
    /**
     * @return ActiveRecord[]
     */
    public function all() {
        $rows = $this->createCommand()->queryAll();
        return $this->populate($rows);
    }
    /**
     * @param string|array $select
     * @return self
     */
    public function select($select) {
        $this->select = is_array($select) ? $select : [$select];
        return $this;
    }
    /**
     * @param string|array $from
     * @return self
     */
    public function from($from) {
        $this->from = is_array($from) ? $from : [$from];
        return $this;
    }
    /**
     * @param string $type
     * @param string $table
     * @param string $on
     */
    public function join($type, $table, $on) {
        $this->join[] = [$type, $table, $on];
        return $this;
    }
    public function innerJoin($table, $on) {
        return $this->join('INNER JOIN', $table, $on);
    }
    public function leftJoin($table, $on) {
        return $this->join('LEFT JOIN', $table, $on);
    }
    /**
     * @param string|array $condition
     * @return self
     */
    public function where($condition) {
        $this->where = $condition;
        return $this;
    }
    /**
     * @param string|array $condition
     * @return self
     */
    public function andWhere($condition) {
        if ($this->where === null) {
            $this->where = $condition;
        }
        elseif (is_array($this->where) && isset($this->where[0]) && strcasecmp($this->where[0], 'and') === 0) {
            $this->where[] = $condition;
        }
        else {
            $this->where = ['and', $this->where, $condition];
        }
        return $this;
    }
    /**
     * @param string|array $condition
     * @return self
     */
    public function orWhere($condition) {
        if ($this->where === null) {
            $this->where = $condition;
        }
        else {
            $this->where = ['or', $this->where, $condition];
        }
        return $this;
    }
    /**
     * @param string|array $sort
     * @return self
     */
    public function order($sort) {
        $this->orderBy = is_array($sort) ? $sort : [$sort => SORT_ASC];
        return $this;
    }
    /**
     * @param int|string $limit
     * @return self
     */
    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }
    /**
     * @param int|string $offset
     * @return self
     */
    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }
    /**
     * @return Command
     */
    public function createCommand() {
        $command = $this->db->createCommand()->select($this);
        return $command;
    }
    /**
     * @param array $rows
     * @return ActiveRecord[]
     */
    public function populate(array $rows = []) {
        if (empty($rows)) {
            return [];
        }
        $models = [];
        foreach ($rows as $index => $row) {
            /* @var $model ActiveRecord */
            $model    = Me::createObject(['class' => $this->modelClass]);
            $model->populateRecord($row);
            $models[] = $model;
        }
        return $models;
    }
    /**
     * @param QueryBuilder $builder
     * @return ActiveQuery
     */
    public function prepare(QueryBuilder $builder) {
        if (empty($this->select)) {
            $this->select = [];
        }
        if (empty($this->from)) {
            $this->from = [$this->getPrimaryTableName()];
        }
        if (empty($this->join)) {
            $this->join = [];
        }
        if (empty($this->groupBy)) {
            $this->groupBy = [];
        }
        if (empty($this->orderBy)) {
            $this->orderBy = [];
        }
        if (is_null($this->limit)) {
            $this->limit = '';
        }
        else {
            $this->limit = (string) $this->limit;
        }
        if (is_null($this->offset)) {
            $this->offset = '';
        }
        else {
            $this->offset = (string) $this->offset;
        }
        return $this;
    }
    /**
     * @return string
     */
    protected function getPrimaryTableName() {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        return $modelClass::tablename();
    }
}