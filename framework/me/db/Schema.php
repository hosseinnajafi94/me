<?php
namespace me\db;
use Me;
use me\components\Component;
/**
 * @property-read QueryBuilder $queryBilder
 */
class Schema extends Component {
    const TYPE_PK                 = 'pk';
    const TYPE_UPK                = 'upk';
    const TYPE_BIGPK              = 'bigpk';
    const TYPE_UBIGPK             = 'ubigpk';
    const TYPE_CHAR               = 'char';
    const TYPE_STRING             = 'string';
    const TYPE_TEXT               = 'text';
    const TYPE_TINYINT            = 'tinyint';
    const TYPE_SMALLINT           = 'smallint';
    const TYPE_INTEGER            = 'integer';
    const TYPE_BIGINT             = 'bigint';
    const TYPE_FLOAT              = 'float';
    const TYPE_DOUBLE             = 'double';
    const TYPE_DECIMAL            = 'decimal';
    const TYPE_DATETIME           = 'datetime';
    const TYPE_TIMESTAMP          = 'timestamp';
    const TYPE_TIME               = 'time';
    const TYPE_DATE               = 'date';
    const TYPE_BINARY             = 'binary';
    const TYPE_BOOLEAN            = 'boolean';
    const TYPE_MONEY              = 'money';
    const TYPE_JSON               = 'json';
    /**
     * @var Db
     */
    public $db;
    /**
     * @var array
     */
    protected $_tableSchema = [];
    /**
     * @param string $sql
     * @return Command
     */
    public function createCommand(string $sql = null, array $params = []) {
        return Me::createObject([
                    'class'  => Command::class,
                    'db'     => $this->db,
                    'sql'    => $sql,
                    'params' => $params
        ]);
    }
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
        
    }
    /**
     * @param TableSchema $table
     * @return TableSchema
     */
    public function findColumns(TableSchema $table) {
        
    }
    /**
     * @param array $info
     * @return ColumnSchema
     */
    public function loadColumnSchema(array $info) {
        
    }
    /**
     * @return ColumnSchema
     */
    public function createColumnSchema() {
        return new ColumnSchema();
    }
    /**
     * @param ColumnSchema $column
     * @return string
     */
    protected function getColumnPhpType($column) {
        static $typeMap = [
            // abstract type => php type
            self::TYPE_TINYINT  => 'integer',
            self::TYPE_SMALLINT => 'integer',
            self::TYPE_INTEGER  => 'integer',
            self::TYPE_BIGINT   => 'integer',
            self::TYPE_BOOLEAN  => 'boolean',
            self::TYPE_FLOAT    => 'double',
            self::TYPE_DOUBLE   => 'double',
            self::TYPE_BINARY   => 'resource',
            self::TYPE_JSON     => 'array',
        ];
        if (isset($typeMap[$column->type])) {
            if ($column->type === 'bigint') {
                return PHP_INT_SIZE === 8 && !$column->unsigned ? 'integer' : 'string';
            }
            elseif ($column->type === 'integer') {
                return PHP_INT_SIZE === 4 && $column->unsigned ? 'string' : 'integer';
            }
            return $typeMap[$column->type];
        }
        return 'string';
    }
    /**
     * @param mixed $value
     * @return int PDO Param Type
     */
    public function getPdoType($value) {
        return \PDO::PARAM_STR;
    }
}