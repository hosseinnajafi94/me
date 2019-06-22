<?php
namespace app\modules\dashboard\controllers;
use me\components\Controller;
class DefaultController extends Controller {
    public $access = [
        'class' => 'me\components\Access',
        'rules' => [
            [
                'actions' => ['index'],
                'roles'   => ['dashboard'],
                'verbs'   => ['GET']
            ],
        ]
    ];
    public function Index() {
        return $this->view();
    }
}