<?php
namespace app\modules\users\controllers;
use Me;
use me\components\Controller;
use app\modules\users\models\SRL\ProfileSRL;
class ProfileController extends Controller {
    public $access = [
        'class' => 'me\components\Access',
        'rules' => [
            [
                'actions' => ['index'],
                'roles'   => ['profile'],
                'verbs'   => ['GET']
            ],
            [
                'actions' => ['avatar', 'password', 'update'],
                'roles'   => ['profile'],
                'verbs'   => ['GET', 'POST']
            ]
        ]
    ];
    public function Index() {
        $model = Me::$app->user->identity;
        return $this->view(['model' => $model]);
    }
    public function Avatar() {
        $model = ProfileSRL::findAvatar(Me::$app->user->identity);
        if ($model->loadFiles(files()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->view(['model' => $model]);
    }
    public function Password() {
        $model = ProfileSRL::findPassword(Me::$app->user->identity);
        if ($model->load(post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->view(['model' => $model]);
    }
    public function Update() {
        $model = ProfileSRL::findUpdate(Me::$app->user->identity);
        if ($model->load(post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->view(['model' => $model]);
    }
}