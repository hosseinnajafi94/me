<?php
namespace app\modules\users\models\VML;
use Me;
use me\components\Model;
use app\modules\users\models\DAL\Users;
class ProfilePasswordVML extends Model {
    /**
     * @var Users
     */
    private $_model;
    /**
     * @var string
     */
    public $old_password;
    /**
     * @var string
     */
    public $new_password;
    /**
     * @var string
     */
    public $new_password_repeat;
    /**
     * @return array Rules
     */
    public function rules() {
        return [
                [['old_password', 'new_password', 'new_password_repeat'], 'required'],
                [['old_password', 'new_password', 'new_password_repeat'], 'string', 'min' => 6, 'max' => 255],
                //[['new_password'], 'compare', 'width' => 'new_password_repeat'],
        ];
    }
    /**
     * @return array Labels
     */
    public function labels() {
        return [
            'old_password'        => Me::t('users', 'Old Password'),
            'new_password'        => Me::t('users', 'New Password'),
            'new_password_repeat' => Me::t('users', 'New Password Repeat'),
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
        $model   = $this->_model;
        $isValid = Me::$app->security->validatePassword($model->password, $this->old_password);
        if (!$isValid) {
            $this->addError('old_password', 'رمز عبور فعلی اشتباه می باشد.');
            return false;
        }
        $model->password = Me::$app->security->generatePasswordHash($this->new_password);
        return $model->save();
    }
}