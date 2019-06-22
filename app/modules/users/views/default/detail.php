<?php
use me\helpers\Html;
use me\widgets\DetailView;
/* @var $this me\components\View */
/* @var $model app\modules\users\models\DAL\Users */
$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => Me::t('users', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->fullname;
?>
<div id="users-default-detail">
    <div class="box">
        <div class="box-header"><?= $this->title ?></div>
        <p>
            <?= Html::a(Me::t('site', 'Return'), ['index'], ['class' => 'btn btn-sm btn-warning']) ?>
            <?= Html::a(Me::t('site', 'Create'), ['create'], ['class' => 'btn btn-sm btn-success']) ?>
            <?= Html::a(Me::t('site', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
            <?= Html::a(Me::t('site', 'Delete'), ['delete', 'id' => $model->id], ['class' => 'btn btn-sm btn-danger', 'data-method' => 'post', 'data-confirm' => Me::t('site', 'Are you sure?')]) ?>
        </p>
        <div class="table-responsive">
            <?= DetailView::widget([
                'model' => $model,
                'columns' => [
                    'id',
                    'fullname',
                    'username',
                    'password',
                ]
            ]) ?>
        </div>
    </div>
</div>