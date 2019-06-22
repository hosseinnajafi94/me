<?php
namespace app\modules\users\models\SRL;
use app\modules\users\models\DAL\Users;
use app\modules\users\models\VML\ProfileAvatarVML;
use app\modules\users\models\VML\ProfilePasswordVML;
use app\modules\users\models\VML\ProfileUpdateVML;
class ProfileSRL {
    /**
     * @param int $id User Identity Number
     * @return ProfileAvatarVML Avatar View Model
     */
    public static function findAvatar(int $id) {
        /* @var $model Users */
        $model = Users::findOne($id);
        if ($model === null) {
            return null;
        }
        $data = new ProfileAvatarVML();
        $data->_set_model($model);
        $data->avatar = $model->avatar;
        return $data;
    }
    /**
     * @param int $id User Identity Number
     * @return ProfilePasswordVML Avatar View Model
     */
    public static function findPassword(int $id) {
        /* @var $model Users */
        $model = Users::findOne($id);
        if ($model === null) {
            return null;
        }
        $data = new ProfilePasswordVML();
        $data->_set_model($model);
        return $data;
    }
    /**
     * @param int $id User Identity Number
     * @return ProfileUpdateVML Update View Model
     */
    public static function findUpdate(int $id) {
        /* @var $model Users */
        $model = Users::findOne($id);
        if ($model === null) {
            return null;
        }
        $data = new ProfileUpdateVML();
        $data->_set_model($model);
        $data->username = $model->username;
        $data->fullname = $model->fullname;
        return $data;
    }
}