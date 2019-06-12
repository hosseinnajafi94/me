<?php
namespace me\db\mysql;
use Me;
use me\db\TableSchema;
use me\db\ColumnSchema;
class Schema extends \me\db\Schema {
    /**
     * @var array mapping from physical column types (keys) to abstract column types (values)
     */
    public $typeMap = [
        'tinyint'    => self::TYPE_TINYINT,
        'bit'        => self::TYPE_INTEGER,
        'smallint'   => self::TYPE_SMALLINT,
        'mediumint'  => self::TYPE_INTEGER,
        'int'        => self::TYPE_INTEGER,
        'integer'    => self::TYPE_INTEGER,
        'bigint'     => self::TYPE_BIGINT,
        'float'      => self::TYPE_FLOAT,
        'double'     => self::TYPE_DOUBLE,
        'real'       => self::TYPE_FLOAT,
        'decimal'    => self::TYPE_DECIMAL,
        'numeric'    => self::TYPE_DECIMAL,
        'tinytext'   => self::TYPE_TEXT,
        'mediumtext' => self::TYPE_TEXT,
        'longtext'   => self::TYPE_TEXT,
        'longblob'   => self::TYPE_BINARY,
        'blob'       => self::TYPE_BINARY,
        'text'       => self::TYPE_TEXT,
        'varchar'    => self::TYPE_STRING,
        'string'     => self::TYPE_STRING,
        'char'       => self::TYPE_CHAR,
        'datetime'   => self::TYPE_DATETIME,
        'year'       => self::TYPE_DATE,
        'date'       => self::TYPE_DATE,
        'time'       => self::TYPE_TIME,
        'timestamp'  => self::TYPE_TIMESTAMP,
        'enum'       => self::TYPE_STRING,
        'varbinary'  => self::TYPE_BINARY,
        'json'       => self::TYPE_JSON,
    ];
    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder() {
        return Me::createObject([
                    'class' => QueryBuilder::class,
                    'db'    => $this->db
        ]);
    }
    /**
     * @param string $name Table Name
     * @return TableSchema
     */
    public function getTableSchema(string $name = null) {
        if ($name === null) {
            return null;
        }
        if (!isset($this->_tableSchema[$name])) {
            $table                     = new TableSchema(['name' => $name]);
            $this->_tableSchema[$name] = $this->findColumns($table);
        }
        return $this->_tableSchema[$name];
    }
    /**
     * @param TableSchema $table
     * @return TableSchema
     */
    public function findColumns(TableSchema $table) {
        $sql     = "SHOW FULL COLUMNS FROM `$table->name`";
        $columns = $this->db->createCommand($sql)->queryAll();
        foreach ($columns as $info) {
            $info                          = array_change_key_case($info, CASE_LOWER);
            $column                        = $this->loadColumnSchema($info);
            $table->columns[$column->name] = $column;
            if ($column->isPrimaryKey) {
                $table->primaryKey = $column->name;
            }
        }
        return $table;
    }
    /**
     * @param array $info
     * @return ColumnSchema
     */
    public function loadColumnSchema(array $info) {
        $column                = $this->createColumnSchema();
        $column->name          = $info['field'];
        $column->dbType        = $info['type'];
        $column->allowNull     = $info['null'] === 'YES';
        $column->isPrimaryKey  = strpos($info['key'], 'PRI') !== false;
        $column->defaultValue  = $info['default'];
        $column->autoIncrement = stripos($info['extra'], 'auto_increment') !== false;
        $column->comment       = $info['comment'];
        $column->unsigned      = stripos($column->dbType, 'unsigned') !== false;
        $column->type          = self::TYPE_STRING;
        $matches               = [];
        if (preg_match('/^(\w+)(?:\(([^\)]+)\))?/', $column->dbType, $matches)) {
            $type = strtolower($matches[1]);
            if (isset($this->typeMap[$type])) {
                $column->type = $this->typeMap[$type];
            }
            if (!empty($matches[2])) {
                if ($type === 'enum') {
                    $values = [];
                    preg_match_all("/'[^']*'/", $matches[2], $values);
                    foreach ($values[0] as $i => $value) {
                        $values[$i] = trim($value, "'");
                    }
                    $column->enumValues = $values;
                }
                else {
                    $values            = explode(',', $matches[2]);
                    $column->size      = $column->precision = (int) $values[0];
                    if (isset($values[1])) {
                        $column->scale = (int) $values[1];
                    }
                    if ($column->size === 1 && $type === 'bit') {
                        $column->type = 'boolean';
                    }
                    elseif ($type === 'bit') {
                        if ($column->size > 32) {
                            $column->type = 'bigint';
                        }
                        elseif ($column->size === 32) {
                            $column->type = 'integer';
                        }
                    }
                }
            }
        }
        $column->phpType = $this->getColumnPhpType($column);
        if (!$column->isPrimaryKey) {
            if (isset($type) && $type === 'bit') {
                $column->defaultValue = bindec(trim($info['default'], 'b\''));
            }
            else {
                $column->defaultValue = $column->typecast($info['default']);
            }
        }
        return $column;
    }
}