<?php
namespace me\components;
use me\helpers\StringHelper;
use Me;
class ActiveQuery extends Model {
    public $modelClass;
    private $_select;
    private $_from;
    public function __construct($config = []) {
        parent::__construct($config);
        $this->_select = '*';
        $this->_from   = self::tablename();
    }
    public function tablename() {
        return StringHelper::camel2id(basename($this->modelClass));
    }
    /**
     * @return ActiveQuery
     */
    public static function find() {
        return Me::createObject(static::class);
    }
    public function select($select) {
        $this->_select = $select;
        return $this;
    }
    public function from($from) {

        return $this;
    }
    public function where($conditions) {

        return $this;
    }
    public function order($sort) {

        return $this;
    }
    public function limit($limit) {

        return $this;
    }
    public function offset($offset) {

        return $this;
    }
    public function one() {
        $sql = "SELECT $this->_select FROM $this->_from";
        if ($this->_where) {
            $sql .= " WHERE $this->_where";
        }
        if ($this->_order) {
            $sql .= " ORDER BY $this->_order";
        }
        if ($this->_limit) {
            $sql .= " LIMIT $this->_limit";
        }
        if ($this->_offset) {
            $sql .= " OFFSET $this->_offset";
        }
        $model = new $this->modelClass;

        return $model;
    }
    public function all() {

        $sql = "SELECT $this->_select FROM $this->_from";

        if ($this->_where) {
            $sql .= " WHERE $this->_where";
        }
        if ($this->_order) {
            $sql .= " ORDER BY $this->_order";
        }
        if ($this->_limit) {
            $sql .= " LIMIT $this->_limit";
        }
        if ($this->_offset) {
            $sql .= " OFFSET $this->_offset";
        }

        $models = [];
        return $models;
    }
    public static function findOne($conditions) {

        $sql = "SELECT * FROM :table WHERE ";

        $model = new static;
        return $model;
    }
    public static function findAll($conditions) {

        $table = self::tablename();
        $sql   = "SELECT * FROM $table";

        $models = [];
        return $models;
    }
}