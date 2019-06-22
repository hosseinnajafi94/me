<?php
namespace app\modules\users\models\VML;
use Me;
use me\components\Model;
use app\modules\users\models\DAL\Users;
class UsersUpdateVML extends Model {
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $fullname;
    /**
     * @var Users
     */
    private $_model;
    /**
     * @return array Rules
     */
    public function rules() {
        return [
            [['username', 'fullname'], 'required'],
            [['username', 'fullname'], 'string', 'min' => 6, 'max' => 255],
        ];
    }
    /**
     * @return array Labels
     */
    public function labels() {
        return [
            'username' => Me::t('users', 'Username'),
            'fullname' => Me::t('users', 'Fullname'),
        ];
    }
    /**
     * @param Users $model User Model
     * @return void
     */
    public function _set_model(Users $model) {
        $this->_model = $model;
    }
    /**
     * @return bool
     */
    public function save() {
        if (!$this->validate()) {
            return false;
        }
        $model = $this->_model;
        $model->fullname = $this->fullname;
        $model->username = $this->username;
        $saved = $model->save();
        if ($saved) {
            $this->id = $model->id;
        }
        return $saved;
    }
}