<?php
namespace app\modules\users\models\VML;
use Me;
use me\components\Model;
use app\modules\users\models\DAL\Users;
class UsersCreateVML extends Model {
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
     * @return bool
     */
    public function save() {
        if (!$this->validate()) {
            return false;
        }
        $model = new Users();
        $model->fullname = $this->fullname;
        $model->username = $this->username;
        $model->password = Me::$app->security->generatePasswordHash('123456');
        $model->avatar = 'default.png';
        $saved = $model->save();
        if ($saved) {
            $this->id = $model->id;
        }
        return $saved;
    }
}