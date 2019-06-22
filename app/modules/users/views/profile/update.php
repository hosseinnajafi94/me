<?php
use me\helpers\Html;
use me\widgets\ActiveForm;
/* @var $this me\components\View */
/* @var $form me\widgets\ActiveForm */
/* @var $model app\modules\users\models\VML\ProfileUpdateVML */
$this->title = Me::t('users', 'Update Profile');
$this->params['breadcrumbs'][] = ['label' => Me::t('users', 'Profile'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Me::t('users', 'Update Profile');
?>
<div id="users-profile-update">
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