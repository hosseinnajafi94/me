<?php
use me\widgets\Alert;
use app\assets\LoginAsset;
/* @var $this me\components\Controller */
/* @var $content string */
LoginAsset::register($this);
?>
<!Doctype HTML>
<html lang="<?= Me::$app->language ?>">
    <head>
        <?= $this->head() ?>
    </head>
    <body dir="<?= Me::$app->dir ?>">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
                    <?= Alert::widget() ?>
                    <?= $content ?>
                </div>
            </div>
        </div>
        <?= $this->body() ?>
    </body>
</html>