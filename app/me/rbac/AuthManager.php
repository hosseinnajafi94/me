<?php
namespace me\rbac;
use Me;
use me\db\ActiveQuery;
use me\components\Component;
class AuthManager extends Component {
    /**
     * @var \me\db\Db
     */
    public $db;
    /**
     * @var string
     */
    public $assignmentTable = 'auth_assignment';
    /**
     * @var string
     */
    public $itemTable       = 'auth_item';
    /**
     * @var string
     */
    public $itemChildTable  = 'auth_item_child';
    /**
     * @var array
     */
    private $_assignments   = [];
    /**
     * 
     */
    public function init() {
        parent::init();
        $this->db = Me::$app->getDb();
    }
    /**
     * @param int $userId Identity Number
     * @param string $permissionName Permission Name
     * @return bool
     */
    public function checkAccess(int $userId, string $permissionName): bool {
        if (isset($this->_assignments[$userId])) {
            $assignments = $this->_assignments[$userId];
        }
        else {
            $assignments                 = $this->getAssignments($userId);
            $this->_assignments[$userId] = $assignments;
        }
        if (empty($assignments)) {
            return false;
        }
        return $this->checkAccessRecursive($userId, $permissionName, $assignments);
    }
    /**
     * @param int $userId Identity Number
     * @param string $permissionName Permission Name
     * @param array $assignments Assignments
     * @return bool
     */
    protected function checkAccessRecursive(int $userId, string $permissionName, array $assignments) {
        $item = $this->getItem($permissionName);
        if ($item === null) {
            return false;
        }
        if (isset($assignments[$item->id])) {
            return true;
        }
        $parents = (new ActiveQuery(['db' => $this->db]))
                ->select("$this->itemTable.name")
                ->from($this->itemChildTable)
                ->innerJoin($this->itemTable, "$this->itemChildTable.parent_id = $this->itemTable.id")
                ->where(["$this->itemChildTable.child_id" => $item->id])
                ->createCommand();
        $parents = $parents->queryAll();
        foreach ($parents as $parent) {
            if ($this->checkAccessRecursive($userId, $parent['name'], $assignments)) {
                return true;
            }
        }
        return false;
    }
    /**
     * @param int $userId Identity Number
     * @return Assignment[]
     */
    public function getAssignments(int $userId) {
        if ($userId === 0) {
            return [];
        }
        $rows        = (new ActiveQuery(['db' => $this->db]))
                        ->from($this->assignmentTable)
                        ->where(['user_id' => $userId])
                        ->createCommand()->queryAll();
        $assignments = [];
        foreach ($rows as $row) {
            $assignments[intval($row['item_id'])] = new Assignment([
                'id'      => $row['id'],
                'item_id' => $row['item_id'],
                'user_id' => $row['user_id'],
            ]);
        }
        return $assignments;
    }
    /**
     * @param string $name Item Name
     * @return Item
     */
    protected function getItem(string $name) {
        if (empty($name)) {
            return null;
        }
        $row = (new ActiveQuery(['db' => $this->db]))
                ->from($this->itemTable)
                ->where(['name' => $name])
                ->createCommand()
                ->queryOne();
        if (!$row) {
            return null;
        }
        $class = $row['type'] == Item::TYPE_PERMISSION ? Permission::class : Role::class;
        return new $class(['id' => intval($row['id']), 'name' => $row['name']]);
    }
}