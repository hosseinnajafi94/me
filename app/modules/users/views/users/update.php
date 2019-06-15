<?php
use me\helpers\Html;
use me\widgets\ActiveForm;
/* @var $this me\components\Controller */
/* @var $form ActiveForm */
$this->title     = 'Update User';
?>
<div id="users-users-create">
    <div id="users-users-form">
        <div class="box">
            <?php $form            = ActiveForm::begin(['options' => ['id' => 'test']]) ?>
            <div class="box-header"><?= $this->title ?></div>
            <?= $form->field($model, 'username')->textInput() ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'fullname')->textInput() ?>
            <div class="box-footer">
                <?= Html::a('Return', ['index'], ['class' => 'btn btn-sm btn-warning']) ?>
                <?= Html::submitButton('Save', ['class' => 'btn btn-sm btn-primary']) ?>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>