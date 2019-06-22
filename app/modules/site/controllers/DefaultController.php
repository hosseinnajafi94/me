<?php
namespace app\modules\site\controllers;
use me\components\Controller;
class DefaultController extends Controller {
    public $layout = 'site';
    public function Index() {
        return $this->view();
    }
    public function Error($message) {
        return $this->view(['message' => $message]);
    }
}