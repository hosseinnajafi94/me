<?php
namespace app\modules\users\models\VML;
use Me;
use me\components\Model;
use app\modules\users\models\DAL\Users;
class SignupVML extends Model {
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $password;
    /**
     * @var string
     */
    public $fullname;
    /**
     * @return array
     */
    public function rules(): array {
        return [
            [['username', 'password', 'fullname'], 'required'],
            [['username', 'password', 'fullname'], 'string', 'min' => 6, 'max' => 255],
        ];
    }
    /**
     * @return array
     */
    public function labels(): array {
        return [
            'username' => Me::t('users', 'Username'),
            'password' => Me::t('users', 'Password'),
            'fullname' => Me::t('users', 'Fullname'),
        ];
    }
    /**
     * @return bool
     */
    public function signup(): bool {
        /* @var $user Users */
        $user = Users::findOne(['username' => $this->username]);
        if ($user !== null) {
            $this->addError('username', 'این نام کاربری قبلا در سیستم ثبت شده.');
            return false;
        }
        $model = new Users();
        $model->username = $this->username;
        $model->password = Me::$app->security->generatePasswordHash($this->password);
        $model->fullname = $this->fullname;
        if (!$model->save()) {
            $this->addError('username', 'خطا در ذخیره اطلاعات!');
            return false;
        }
        return Me::$app->user->signin($model);
    }
}