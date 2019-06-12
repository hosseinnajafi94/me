<?php
namespace me\db;
use PDO;
use me\components\Component;
class Command extends Component {
    /**
     * @var Db
     */
    public $db;
    /**
     * @var string
     */
    public $sql;
    /**
     * @var array
     */
    public $params;
    /**
     * @var int
     */
    public $fetchMode = PDO::FETCH_ASSOC;
    /**
     * @var \PDOStatement
     */
    public $pdoStatement;
    /**
     * @param int $fetchMode Fetch Mode
     * @return array|false
     */
    public function queryAll(int $fetchMode = null) {
        return $this->queryInternal('fetchAll', $fetchMode);
    }
    /**
     * @param int $fetchMode Fetch Mode
     * @return array|null|false
     */
    public function queryOne(int $fetchMode = null) {
        return $this->queryInternal('fetch', $fetchMode);
    }
    /**
     * @return int|false Affected Rows
     */
    public function execute() {
        $this->prepare();
        $this->pdoStatement->execute();
        $result = $this->pdoStatement->rowCount();
        $this->pdoStatement->closeCursor();
        return $result;
    }
    /**
     * @param ActiveQuery $query
     * @return self
     */
    public function select(ActiveQuery $query) {
        $params    = [];
        $this->sql = $this->db->createQueryBuilder()->build($query, $params);
        return $this->bindValues($params);
    }
    /**
     * @param string $table
     * @param array|string $condition
     * @return self
     */
    public function delete(string $table, $condition = '') {
        $params    = [];
        $this->sql = $this->db->createQueryBuilder()->delete($table, $condition, $params);
        return $this->bindValues($params);
    }
    /**
     * @param string $table
     * @param array $columns
     * @return self
     */
    public function insert(string $table, array $columns = []) {
        $params    = [];
        $this->sql = $this->db->createQueryBuilder()->insert($table, $columns, $params);
        return $this->bindValues($params);
    }
    /**
     * @param string $table
     * @param array $columns
     * @param array|string $condition
     * @return self
     */
    public function update(string $table, array $columns = [], $condition = '') {
        $params    = [];
        $this->sql = $this->db->createQueryBuilder()->update($table, $columns, $condition, $params);
        return $this->bindValues($params);
    }
    /**
     * @param array $values
     * @return self
     */
    protected function bindValues(array $values = []) {
        if (empty($values)) {
            return $this;
        }
        $schema = $this->db->getSchema();
        foreach ($values as $name => $value) {
            $type                = $schema->getPdoType($value);
            $this->params[$name] = [$value, $type];
        }
        return $this;
    }
    /**
     * @return self
     */
    protected function prepare() {
        $this->pdoStatement = $this->db->pdo->prepare($this->sql);
        foreach ($this->params as $key => $value) {
            $this->pdoStatement->bindValue($key, $value[0], $value[1]);
        }
        return $this;
    }
    /**
     * @param string $method Method [fetch, fetchAll]
     * @param int $fetchMode Fetch Mode
     * @return array|null|false
     */
    protected function queryInternal(string $method, int $fetchMode = null) {
        if ($fetchMode === null) {
            $fetchMode = $this->fetchMode;
        }
        $this->prepare();
        $this->pdoStatement->execute();
        $result = call_user_func_array([$this->pdoStatement, $method], (array) $fetchMode);
        $this->pdoStatement->closeCursor();
        return $result;
    }
}