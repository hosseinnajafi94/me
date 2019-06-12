<?php
namespace me\components;
use Me;
class ActiveRecord extends Model {
    private $_attributes = [];
    /**
     * @return ActiveQuery
     */
    public static function find() {
        return Me::createObject([
                    'class'      => ActiveQuery::class,
                    'modelClass' => get_called_class()
        ]);
    }
    /**
     * @return self
     */
    public static function findOne($condition) {
        return static::findByCondition($condition)->one();
    }
    /**
     * @return array
     */
    public static function findAll($condition) {
        return static::findByCondition($condition)->all();
    }
    /**
     * @return ActiveQuery
     */
    protected static function findByCondition($condition) {
        $query = static::find();
        return $query->where($condition);
    }
    public function __get($name) {
        if (isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        }
        if ($this->hasAttribute($name)) {
            return null;
        }
        return parent::__get($name);
    }
    public function hasAttribute($name) {
        return isset($this->_attributes[$name]) || in_array($name, $this->attributes(), true);
    }
}