<?php
namespace app\modules\users\models\VML;
use Me;
use me\components\Model;
use app\modules\users\models\DAL\Users;
class ProfileUpdateVML extends Model {
    /**
     * @var Users
     */
    private $_model;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $fullname;
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
        /* @var $model Users */
        $model = $this->_model;
        $model->username = $this->username;
        $model->fullname = $this->fullname;
        return $model->save();
    }
}