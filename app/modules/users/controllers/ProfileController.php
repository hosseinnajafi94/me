<?php
namespace app\modules\users\controllers;
use Me;
use me\components\Controller;
use app\components\functions;
use app\modules\users\models\SRL\ProfileSRL;
class ProfileController extends Controller {
    public function Index() {
        $model = Me::$app->user->identity;
        return $this->view(['model' => $model]);
    }
    public function Avatar() {
        $model = ProfileSRL::findAvatar(Me::$app->user->id);
        if ($model === null) {
            return functions::httpNotFound();
        }
        if ($model->loadFiles(files()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->view(['model' => $model]);
    }
    public function Password() {
        $model = ProfileSRL::findPassword(Me::$app->user->id);
        if ($model === null) {
            return functions::httpNotFound();
        }
        if ($model->load(post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->view(['model' => $model]);
    }
    public function Update() {
        $model = ProfileSRL::findUpdate(Me::$app->user->id);
        if ($model === null) {
            return functions::httpNotFound();
        }
        if ($model->load(post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->view(['model' => $model]);
    }
}