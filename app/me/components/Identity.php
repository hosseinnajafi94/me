<?php
namespace me\components;
use me\db\ActiveRecord;
/**
 * @property-read int $id
 * @property string $username
 * @property string $password
 */
class Identity extends ActiveRecord implements IdentityInterface {
    public static function tablename() {
        return 'users';
    }
    public static function findIdentity(int $id) {
        return static::findOne($id);
    }
    public function getId(): int {
        return intval($this->id);
    }
}