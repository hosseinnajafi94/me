<?php
namespace app\modules\users\controllers;

use me\components\Controller;
class UsersController extends Controller
{
    public function Index()
    {
        return $this->view([]);
    }
    public function Create()
    {
    }
    public function Detail($id)
    {
    }
    public function Update($id)
    {
    }
    public function Delete($id)
    {
    }
    public static function findModel($id)
    {
        $model = Users::findOne($id);
        return $model;
    }
}