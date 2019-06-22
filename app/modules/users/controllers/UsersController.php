<?php
namespace app\modules\users\controllers;
use me\components\Controller;
use app\components\functions;
use app\modules\users\models\SRL\UsersSRL;
class UsersController extends Controller {
    public $access = [
        'class' => 'me\components\Access',
        'rules' => [
            [
                'actions' => ['index', 'detail'],
                'roles'   => ['userManagment'],
                'verbs'   => ['GET']
            ],
            [
                'actions' => ['delete'],
                'roles'   => ['userManagment'],
                'verbs'   => ['POST']
            ],
            [
                'actions' => ['create', 'update'],
                'roles'   => ['userManagment'],
                'verbs'   => ['GET', 'POST']
            ]
        ]
    ];
    public function Index() {
        $data = UsersSRL::search();
        return $this->view(['data' => $data]);
    }
    public function Create() {
        $model = UsersSRL::newModel();
        if ($model->load(post()) && $model->save()) {
            return $this->redirect(['detail', 'id' => $model->id]);
        }
        return $this->view(['model' => $model]);
    }
    public function Detail($id) {
        $model = UsersSRL::find($id);
        if ($model === null) {
            return functions::httpNotFound();
        }
        return $this->view(['model' => $model]);
    }
    public function Update($id) {
        $model = UsersSRL::findModel($id);
        if ($model === null) {
            return functions::httpNotFound();
        }
        if ($model->load(post()) && $model->save()) {
            return $this->redirect(['detail', 'id' => $model->id]);
        }
        return $this->view(['model' => $model]);
    }
    public function Delete($id) {
        $model = UsersSRL::find($id);
        if ($model === null) {
            return functions::httpNotFound();
        }
        $model->delete();
        return $this->redirect(['index']);
    }
}