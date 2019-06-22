<?php
namespace app\modules\users\models\SRL;
use me\data\ActiveDataProvider;
use app\modules\users\models\DAL\Users;
use app\modules\users\models\VML\UsersCreateVML;
use app\modules\users\models\VML\UsersUpdateVML;
class UsersSRL {
    /**
     * @return ActiveDataProvider
     */
    public static function search(): ActiveDataProvider {
        $query = Users::find();
        $data  = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['id' => SORT_DESC],
            'pagination' => ['size' => 5]
        ]);
        return $data;
    }
    /**
     * @param int $id User Identity Number
     * @return Users User Model
     */
    public static function find(int $id) {
        return Users::findOne($id);
    }
    /**
     * @return UsersCreateVML User View Model
     */
    public static function newModel(): UsersCreateVML {
        $model = new UsersCreateVML();
        return $model;
    }
    /**
     * @param int $id User Identity Number
     * @return UsersUpdateVML User View Model
     */
    public static function findModel(int $id) {
        $model = static::find($id);
        if ($model === null) {
            return null;
        }
        $data = new UsersUpdateVML();
        $data->_set_model($model);
        $data->username = $model->username;
        $data->fullname = $model->fullname;
        return $data;
    }
}