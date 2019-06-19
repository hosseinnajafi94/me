<?php
namespace app\modules\users\controllers;
use me\components\Controller;
use me\data\ActiveDataProvider;
use app\me\functions\functions;
use app\modules\users\models\DAL\Users;
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
        $query = Users::find();
        $data  = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['id' => SORT_DESC],
            'pagination' => ['size' => 5]
        ]);
        return $this->view(['data' => $data]);
    }
    public function Create() {
        $model = new Users();
        if ($model->load(post()) && $model->loadFiles(files()) && $model->save()) {
            return $this->redirect(['detail', 'id' => $model->id]);
        }
        return $this->view(['model' => $model]);
    }
    public function Detail($id) {
        $model = $this->findModel($id);
        return $this->view(['model' => $model]);
    }
    public function Update($id) {
        $model = $this->findModel($id);
        if ($model->load(post()) && $model->loadFiles(files()) && $model->save()) {
            return $this->redirect(['detail', 'id' => $model->id]);
        }
        return $this->view(['model' => $model]);
    }
    public function Delete($id) {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
    /**
     * @param int $id User ID
     * @return Users
     */
    private function findModel($id) {
        $model = Users::findOne($id);
        if ($model === null) {
            return functions::httpNotFound();
        }
        return $model;
    }
}