<?php
/* @var $this me\components\View */
/* @var $model app\modules\users\models\DAL\Users */
$this->title = Me::t('site', 'Create');
$this->params['breadcrumbs'][] = ['label' => Me::t('users', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Me::t('site', 'Create');
?>
<div id="users-users-create">
    <?= $this->view('_form', ['model' => $model]) ?>
</div>