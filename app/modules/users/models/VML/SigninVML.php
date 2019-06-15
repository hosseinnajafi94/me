<?php
namespace app\modules\users\models\VML;
use Me;
use me\components\Model;
use app\modules\users\models\DAL\Users;
class SigninVML extends Model {
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $password;
    /**
     * @return array
     */
    public function rules(): array {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password'], 'string', 'min' => 6, 'max' => 255],
        ];
    }
    /**
     * @return array
     */
    public function labels(): array {
        return [
            'username' => Me::t('users', 'Username'),
            'password' => Me::t('users', 'Password'),
        ];
    }
    /**
     * @return bool
     */
    public function signin(): bool {
        /* @var $user Users */
        $user = Users::findOne(['username' => $this->username]);
        if ($user === null) {
            $this->addError('username', 'نام کاربری اشتباه می باشد.');
            return false;
        }
        $isValid = Me::$app->security->validatePassword($user->password, $this->password);
        if (!$isValid) {
            $this->addError('username', 'رمز عبور اشتباه می باشد.');
            return false;
        }
        return Me::$app->user->signin($user, 60);
    }
}