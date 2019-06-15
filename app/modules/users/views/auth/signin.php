<?php
use me\helpers\Html;
use me\widgets\ActiveForm;
/* @var $this  me\components\Controller */
/* @var $form  me\widgets\ActiveForm */
/* @var $model app\modules\users\models\VML\SigninVML */
$this->title = 'Signin';
?>
<div id="users-auth-signin">
    <?php $form = ActiveForm::begin() ?>
    <div class="panel panel-default">
        <div class="panel-heading">ورود</div>
        <div class="panel-body">
            <?= $form->field($model, 'username') ?>
            <?= $form->field($model, 'password') ?>
        </div>
        <div class="panel-footer text-left">
            <?= Html::submitButton('ورود', ['class' => 'btn btn-sm btn-default']) ?>
        </div>
    </div>
    <?= Html::a('ثبت نام', ['signup']) ?> 
    <?php ActiveForm::end() ?>
</div>