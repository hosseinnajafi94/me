<?php
use me\helpers\Html;
use me\widgets\DetailView;
use app\modules\users\models\DAL\Users;
/* @var $this me\components\View */
/* @var $model app\modules\users\models\DAL\Users */
$this->title = Me::t('users', 'Profile');
$this->params['breadcrumbs'][] = Me::t('users', 'Profile');
?>
<div id="users-profile-index">
    <div class="box">
        <div class="box-header"><?= $model->fullname ?></div>
        <p>
            <?= Html::a(Me::t('users', 'Update Profile'), ['update'], ['class' => 'btn btn-sm btn-default']) ?>
            <?= Html::a(Me::t('users', 'Change Avatar'), ['avatar'], ['class' => 'btn btn-sm btn-default']) ?>
            <?= Html::a(Me::t('users', 'Change Password'), ['password'], ['class' => 'btn btn-sm btn-default']) ?>
        </p>
        <div class="table-responsive">
            <?= DetailView::widget([
                'model' => $model,
                'columns' => [
                    'fullname',
                    'username',
                    [
                        'attribute' => 'avatar',
                        'value'     => function (Users $model): string {
                            $url = Me::getAlias('@web/uploads/users/' . $model->avatar);
                            return Html::img($url, ['style' => 'max-width: 150px;max-height: 150px;']);
                        }
                    ],
                ]
            ]) ?>
        </div>
    </div>
</div>