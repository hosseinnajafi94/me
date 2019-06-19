<?php
/* @var $this me\components\View */
/* @var $model app\modules\users\models\DAL\Users */
$this->title = Me::t('site', 'Update');
$this->params['breadcrumbs'][] = ['label' => Me::t('users', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fullname, 'url' => ['detail', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Me::t('site', 'Update');
?>
<div id="users-users-create">
    <?= $this->view('_form', ['model' => $model]) ?>
</div>