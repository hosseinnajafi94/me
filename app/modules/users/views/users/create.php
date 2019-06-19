<?php
/* @var $this me\components\View */
/* @var $model app\modules\users\models\DAL\Users */
$this->title = 'Create User';
?>
<div id="users-users-create">
    <?= $this->view('_form', ['model' => $model]) ?>
</div>
