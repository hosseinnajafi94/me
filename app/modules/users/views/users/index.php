<?php
use me\helpers\Html;
use me\widgets\GridView;
/* @var $this me\components\Controller */
/* @var $data me\data\ActiveDataProvider */
/* @var $grid me\widgets\GridView */
$this->title = 'Users';
$grid = GridView::widget([
    'data'    => $data,
    'columns' => [
        ['class' => 'me\widgets\SerialNumberColumn'],
        // 'id',
        'fullname',
        'username',
        //'password',
        ['class' => 'me\widgets\ActionColumn'],
    ]
]);
?>
<div id="users-users-index">
    <div class="box">
        <div class="box-header"><?= $this->title ?></div>
        <p>
            <?= Html::a('Create', ['create'], ['class' => 'btn btn-sm btn-success']) ?>
        </p>
        <div class="table-responsive">
            <?= $grid->table ?>
        </div>
        <div class="box-footer">
            <?= $grid->pagination ?>
        </div>
    </div>
</div>