<?php
namespace me\db;
use Me;
use me\components\Model;
use me\helpers\ArrayHelper;
use me\helpers\StringHelper;
/**
 * @property-read bool $isNewRecord
 */
class ActiveRecord extends Model {
    //--------------------------------------------------------------------------
    private $_oldAttributes;
    private $_attributes = [];
    //--------------------------------------------------------------------------
    /**
     * @return array
     */
    public static function attributes(): array {
        return static::getTableSchema()->getColumnNames();
    }
    /**
     * @return string
     */
    public static function tablename() {
        return StringHelper::camel2id(basename(get_called_class()));
    }
    /**
     * @return Db
     */
    public static function getDb() {
        return Me::$app->getDb();
    }
    /**
     * @return TableSchema
     */
    public static function getTableSchema() {
        return static::getDb()->getSchema()->getTableSchema(static::tablename());
    }
    /**
     * @return string
     */
    public static function primaryKey() {
        return static::getTableSchema()->primaryKey;
    }
    /**
     * @return ActiveQuery
     */
    public static function find() {
        return Me::createObject([
                    'class'      => ActiveQuery::class,
                    'db'         => static::getDb(),
                    'modelClass' => get_called_class()
        ]);
    }
    /**
     * @param array|string $condition
     * @return self
     */
    public static function findOne($condition) {
        return static::findByCondition($condition)->one();
    }
    /**
     * @param array|string $condition
     * @return array
     */
    public static function findAll($condition) {
        return static::findByCondition($condition)->all();
    }
    /**
     * @param array|string $condition
     * @return int Affected Rows
     */
    public static function deleteAll($condition) {
        $command = static::getDb()->createCommand();
        $command->delete(static::tablename(), $condition);
        return $command->execute();
    }
    /**
     * @param array $columns
     * @param array|string $condition
     * @return int Affected Rows
     */
    public static function updateAll(array $columns = [], $condition = '') {
        $command = static::getDb()->createCommand();
        $command->update(static::tablename(), $columns, $condition);
        return $command->execute();
    }
    //--------------------------------------------------------------------------
    /**
     * @return bool
     */
    public function save($runValidation = true) {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        if ($this->getIsNewRecord()) {
            return $this->insert();
        }
        return $this->update();
    }
    /**
     * @return bool
     */
    public function delete() {
        $condition    = $this->getOldPrimaryKey(true);
        $affectedRows = static::deleteAll($condition);
        return $affectedRows > 0;
    }
    /**
     * @param bool $asArray
     * @return array|int
     */
    public function getOldPrimaryKey(bool $asArray = false) {
        $key   = static::primaryKey();
        $value = isset($this->_oldAttributes[$key]) ? $this->_oldAttributes[$key] : null;
        if ($asArray) {
            return [$key => $value];
        }
        return $value;
    }
    /**
     * @return bool
     */
    public function getIsNewRecord() {
        return $this->_oldAttributes === null;
    }
    /**
     * @param array $row
     * @return void
     */
    public function populateRecord(array $row) {
        $columns      = $this->getTableSchema()->columns;
        $columnsNames = array_flip($this->attributes());
        foreach ($row as $name => $value) {
            if (isset($columnsNames[$name])) {
                $this->_attributes[$name] = $columns[$name]->typecast($value);
                //$this->_attributes[$name] = $value;
            }
        }
        $this->_oldAttributes = $this->_attributes;
    }
    //--------------------------------------------------------------------------
    /**
     * @return bool
     */
    protected function insert() {
        $columns      = $this->getDirtyAttributes();
        $command      = static::getDb()->createCommand()->insert(static::tablename(), $columns);
        $affectedRows = $command->execute();
        $inserted     = $affectedRows > 0;
        if ($inserted) {
            $name                     = $this->primaryKey();
            $id                       = $this->getLastInsertID();
            $columns[$name]           = $this->getTableSchema()->columns[$name]->typecast($id);
            $this->_attributes[$name] = $columns[$name];
            $this->_oldAttributes     = $columns;
        }
        return $inserted;
    }
    /**
     * @return bool
     */
    protected function update() {
        $columns = $this->getDirtyAttributes();
        if (empty($columns)) {
            return true;
        }
        $condition    = $this->getOldPrimaryKey(true);
        $affectedRows = static::updateAll($columns, $condition);
        $updated      = $affectedRows > 0;
        if ($updated) {
            foreach ($columns as $name => $value) {
                $this->_oldAttributes[$name] = $value;
            }
        }
        return $updated;
    }
    /**
     * @return array
     */
    protected function getDirtyAttributes() {
        $names      = $this->attributes();
        $names      = array_flip($names);
        $attributes = [];
        if ($this->_oldAttributes === null) {
            foreach ($this->_attributes as $name => $value) {
                if (isset($names[$name])) {
                    $attributes[$name] = $value;
                }
            }
        }
        else {
            foreach ($this->_attributes as $name => $value) {
                if (isset($names[$name]) && (!array_key_exists($name, $this->_oldAttributes) || $value !== $this->_oldAttributes[$name])) {
                    $attributes[$name] = $value;
                }
            }
        }
        return $attributes;
    }
    /**
     * @return string Last Insert ID
     */
    protected function getLastInsertID() {
        return static::getDb()->pdo->lastInsertId();
    }
    /**
     * @param array|string $condition
     * @return ActiveQuery
     */
    protected static function findByCondition($condition) {
        $query = static::find();
        if (!ArrayHelper::isAssociative($condition)) {
            $pk        = static::primaryKey();
            $condition = [$pk => $condition];
        }
        return $query->where($condition);
    }
    //--------------------------------------------------------------------------
    /**
     * @param string $name Name
     * @return mixed
     */
    public function __get($name) {
        if (isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        }
        if ($this->hasAttribute($name)) {
            return null;
        }
        return parent::__get($name);
    }
    /**
     * @param string $name Name
     * @param mixed $value 
     * @return void
     */
    public function __set($name, $value) {
        if ($this->hasAttribute($name)) {
            $this->_attributes[$name] = $value;
        }
        else {
            parent::__set($name, $value);
        }
    }
    /**
     * @param string $name Attribute Name
     * @return bool
     */
    public function hasAttribute(string $name): bool {
        return isset($this->_attributes[$name]) || in_array($name, $this->attributes(), true);
    }
}