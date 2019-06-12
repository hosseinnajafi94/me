<?php
namespace me\db;
use PDO;
use Me;
use me\components\Component;
use me\exceptions\NotFound;
/**
 * @property-read Schema $schema
 */
class Db extends Component {
    /**
     * @var PDO
     */
    public $pdo;
    /**
     * @var string
     */
    public $dsn;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $password;
    /**
     * @var array
     */
    public $options;
    /**
     * @var string
     */
    private $driver;
    /**
     * @var Schema
     */
    private $_schema;
    /**
     * @var array
     */
    public $schemaMap = [
        'pgsql'  => 'me\db\pgsql\Schema', // PostgreSQL
        'mysql'  => 'me\db\mysql\Schema', // MySQL
        'sqlsrv' => 'me\db\mssql\Schema', // newer MSSQL driver on MS Windows hosts
        'mssql'  => 'me\db\mssql\Schema', // older MSSQL driver on MS Windows hosts
    ];
    public function init() {
        if ($this->pdo === null) {
            $this->pdo    = new PDO($this->dsn, $this->username, $this->password, $this->options);
            $this->driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        }
    }
    /**
     * @return Schema
     */
    public function getSchema() {
        if ($this->_schema === null) {
            if (!isset($this->schemaMap[$this->driver])) {
                throw new NotFound("Schema { <b>$this->driver</b> } Not Found");
            }
            $this->_schema = Me::createObject([
                        'class' => $this->schemaMap[$this->driver],
                        'db'    => $this
            ]);
        }
        return $this->_schema;
    }
    /**
     * @param string $sql
     * @param array $params
     * @return Command
     */
    public function createCommand(string $sql = null, array $params = []) {
        return $this->getSchema()->createCommand($sql, $params);
    }
    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder() {
        return $this->getSchema()->createQueryBuilder();
    }
}