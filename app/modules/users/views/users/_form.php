<?php
use me\helpers\Html;
use me\widgets\ActiveForm;
/* @var $this me\components\View */
/* @var $form me\widgets\ActiveForm */
/* @var $model app\modules\users\models\DAL\Users */
?>
<div id="users-users-form">
    <div class="box">
        <?php $form = ActiveForm::begin() ?>
        <div class="box-header"><?= $this->title ?></div>
        <?= $form->field($model, 'username')->textInput() ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'fullname')->textInput() ?>
        <?= $form->field($model, 'avatar')->fileInput() ?>
        <div class="box-footer">
            <?= Html::a('Return', ['index'], ['class' => 'btn btn-sm btn-warning']) ?>
            <?= Html::submitButton('Save', ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>