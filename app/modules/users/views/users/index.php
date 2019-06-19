<?php
use me\helpers\Html;
use me\widgets\GridView;
/* @var $this me\components\View */
/* @var $data me\data\ActiveDataProvider */
/* @var $grid me\widgets\GridView */
$this->title = Me::t('users', 'Users');
$this->params['breadcrumbs'][] = Me::t('users', 'Users');
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
            <?= Html::a(Me::t('site', 'Create'), ['create'], ['class' => 'btn btn-sm btn-success']) ?>
        </p>
        <div class="table-responsive">
            <?= $grid->table ?>
        </div>
        <div class="box-footer">
            <?= $grid->pagination ?>
        </div>
    </div>
</div>