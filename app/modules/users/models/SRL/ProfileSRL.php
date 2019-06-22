<?php
namespace app\modules\users\models\SRL;
use app\modules\users\models\DAL\Users;
use app\modules\users\models\VML\ProfileAvatarVML;
use app\modules\users\models\VML\ProfilePasswordVML;
use app\modules\users\models\VML\ProfileUpdateVML;
class ProfileSRL {
    /**
     * @param Users $model User Model
     * @return ProfileAvatarVML Avatar View Model
     */
    public static function findAvatar(Users $model):ProfileAvatarVML {
        $data         = new ProfileAvatarVML();
        $data->avatar = $model->avatar;
        $data->_set_model($model);
        return $data;
    }
    /**
     * @param Users $model User Model
     * @return ProfilePasswordVML Avatar View Model
     */
    public static function findPassword(Users $model): ProfilePasswordVML {
        $data = new ProfilePasswordVML();
        $data->_set_model($model);
        return $data;
    }
    /**
     * @param Users $model User Model
     * @return ProfileUpdateVML Update View Model
     */
    public static function findUpdate(Users $model): ProfileUpdateVML {
        $data           = new ProfileUpdateVML();
        $data->username = $model->username;
        $data->fullname = $model->fullname;
        $data->_set_model($model);
        return $data;
    }
}