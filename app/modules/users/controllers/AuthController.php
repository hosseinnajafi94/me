<?php
namespace app\modules\users\controllers;
use Me;
use me\components\Controller;
use app\modules\users\models\VML\SigninVML;
use app\modules\users\models\VML\SignupVML;
class AuthController extends Controller {
    public function Signin() {
        $model = new SigninVML();
        if ($model->load(post()) && $model->signin()) {
            return $this->redirect(['site/default/index']);
        }
        $this->layout = 'login';
        return $this->view(['model' => $model]);
    }
    public function Signup() {
        $model = new SignupVML();
        if ($model->load(post()) && $model->signup()) {
            return $this->redirect(['site/default/index']);
        }
        $this->layout = 'login';
        return $this->view(['model' => $model]);
    }
    public function Signout() {
        Me::$app->user->signout();
        return $this->redirect(['site/default/index']);
    }
}