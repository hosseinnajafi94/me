<?php
use me\helpers\Url;
use app\assets\SiteAsset;
/* @var $this me\components\View */
/* @var $content string */
SiteAsset::register($this);
?>
<!Doctype HTML>
<html lang="<?= Me::$app->language ?>">
    <head>
        <?= $this->head() ?>
    </head>
    <body dir="<?= Me::$app->dir ?>">
        <header>
            <div class="container">
                <nav class="navbar navbar-default">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="<?= Url::to(['site/default/index']) ?>"><?= Me::t('site', 'ME Framework') ?></a>
                    </div>
                    <div class="collapse navbar-collapse" id="navbar">
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="<?= Url::to(['site/default/index']) ?>"><?= Me::t('site', 'Home') ?></a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-left">
                            <?php
                            if (Me::$app->user->isGuest) {
                                ?>
                                <li><a href="<?= Url::to(['users/auth/signup']) ?>"><span class="glyphicon glyphicon-user"></span> <?= Me::t('site', 'Sign Up') ?></a></li>
                                <li><a href="<?= Url::to(['users/auth/signin']) ?>"><span class="glyphicon glyphicon-log-in"></span> <?= Me::t('site', 'Sign In') ?></a></li>
                                <?php
                            }
                            else {
                                ?>
                                <li><a href="<?= Url::to(['dashboard/default/index']) ?>"><span class="glyphicon glyphicon-dashboard"></span> <?= Me::t('site', 'Dashboard') ?></a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
        <div class="wrapper">
            <div class="container">
                <?= $content ?>
            </div>
            <footer>
                <div class="container">
                    
                </div>
            </footer>
        </div>
        <?= $this->body() ?>
    </body>
</html>