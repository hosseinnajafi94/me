<?php
namespace me\db;
use me\components\Component;
class TableSchema extends Component {
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $primaryKey;
    /**
     * @var array
     */
    public $columns = [];
    /**
     * @param string $name
     * @return ColumnSchema
     */
    public function getColumn($name) {
        return isset($this->columns[$name]) ? $this->columns[$name] : null;
    }
    /**
     * @var array
     */
    public function getColumnNames() {
        return array_keys($this->columns);
    }
}