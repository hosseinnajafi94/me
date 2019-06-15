<?php
use me\helpers\Html;
use me\widgets\ActiveForm;
/* @var $this  me\components\Controller */
/* @var $form  me\widgets\ActiveForm */
/* @var $model app\modules\users\models\VML\SignupVML */
$this->title = 'Signup';
?>
<div id="users-auth-signup">
    <?php $form = ActiveForm::begin() ?>
    <div class="panel panel-default">
        <div class="panel-heading">ثبت نام</div>
        <div class="panel-body">
            <?= $form->field($model, 'username') ?>
            <?= $form->field($model, 'password') ?>
            <?= $form->field($model, 'fullname') ?>
        </div>
        <div class="panel-footer text-left">
            <?= Html::submitButton('ثبت نام', ['class' => 'btn btn-sm btn-default']) ?>
        </div>
    </div>
    <?= Html::a('ورود', ['signin']) ?> 
    <?php ActiveForm::end() ?>
</div>