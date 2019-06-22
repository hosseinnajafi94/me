<?php
use me\helpers\Html;
use me\widgets\ActiveForm;
/* @var $this me\components\View */
/* @var $form me\widgets\ActiveForm */
/* @var $model app\modules\users\models\VML\UsersUpdateVML */
$this->title                   = Me::t('site', 'Update');
$this->params['breadcrumbs'][] = ['label' => Me::t('users', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fullname, 'url' => ['detail', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Me::t('site', 'Update');
?>
<div id="users-default-update">
    <div id="users-users-form">
        <?php $form = ActiveForm::begin() ?>
        <div class="box">
            <div class="box-header"><?= $this->title ?></div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'username')->textInput() ?>
                    <?= $form->field($model, 'fullname')->textInput() ?>
                </div>
            </div>
            <div class="box-footer">
                <?= Html::a(Me::t('site', 'Return'), ['index'], ['class' => 'btn btn-sm btn-warning']) ?>
                <?= Html::submitButton(Me::t('site', 'Save'), ['class' => 'btn btn-sm btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>