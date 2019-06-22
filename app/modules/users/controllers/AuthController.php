<?php
namespace app\modules\users\controllers;
use Me;
use me\components\Controller;
use app\modules\users\models\VML\SigninVML;
use app\modules\users\models\VML\SignupVML;
class AuthController extends Controller {
    public $layout = 'login';
    public function Signin() {
        $model = new SigninVML();
        if ($model->load(post()) && $model->signin()) {
            return $this->redirect(['dashboard/default/index']);
        }
        return $this->view(['model' => $model]);
    }
    public function Signup() {
        $model = new SignupVML();
        if ($model->load(post()) && $model->signup()) {
            return $this->redirect(['dashboard/default/index']);
        }
        return $this->view(['model' => $model]);
    }
    public function Signout() {
        Me::$app->user->signout();
        return $this->redirect(['dashboard/default/index']);
    }
}