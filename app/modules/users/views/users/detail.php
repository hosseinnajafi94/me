<?php
use me\helpers\Html;
use me\widgets\DetailView;
/* @var $this  me\components\Controller */
/* @var $model app\modules\users\models\DAL\Users */
$this->title = $model->fullname;
?>
<div id="users-users-index">
    <div class="box">
        <div class="box-header"><?= $this->title ?></div>
        <p>
            <?= Html::a('Return', ['index'], ['class' => 'btn btn-sm btn-warning']) ?>
            <?= Html::a('Create', ['create'], ['class' => 'btn btn-sm btn-success']) ?>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], ['class' => 'btn btn-sm btn-danger']) ?>
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