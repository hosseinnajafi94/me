<?php
namespace app\modules\users\models\DAL;
use Me;
use me\db\ActiveRecord;
use me\components\IdentityInterface;
/**
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $fullname
 */
class Users extends ActiveRecord implements IdentityInterface {
    public static function tablename() {
        return 'users';
    }
    public function rules() {
        return [
                [['username', 'password', 'fullname'], 'required'],
                [['username', 'password', 'fullname'], 'string', 'max' => 255],
        ];
    }
    public function labels() {
        return [
            'id'       => Me::t('users', 'ID'),
            'username' => Me::t('users', 'Username'),
            'password' => Me::t('users', 'Password'),
            'fullname' => Me::t('users', 'Fullname'),
        ];
    }
    public function hints() {
        return [
        ];
    }
    /**
     * @param int $id Identity Number
     * @return Users|null
     */
    public static function findIdentity(int $id) {
        return static::findOne($id);
    }
    /**
     * @return int Identity Number
     */
    public function getId(): int {
        return intval($this->id);
    }
}