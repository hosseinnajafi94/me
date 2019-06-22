<?php
namespace app\modules\users\models\VML;
use Me;
use me\components\Model;
use app\modules\users\models\DAL\Users;
class ProfileAvatarVML extends Model {
    /**
     * @var Users
     */
    private $_model;
    /**
     * @var string
     */
    public $avatar;
    /**
     * @return array Rules
     */
    public function rules() {
        return [
            [['avatar'], 'required'],
            [['avatar'], 'file', 'path' => Me::getAlias('@root/uploads/users'), 'extensions' => 'png, jpg, jpeg'],
        ];
    }
    /**
     * @return array Labels
     */
    public function labels() {
        return [
            'avatar' => Me::t('users', 'Avatar'),
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
        $model->avatar = $this->avatar;
        return $model->save();
    }
}