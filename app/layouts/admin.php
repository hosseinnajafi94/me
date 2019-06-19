<?php
use me\helpers\Url;
use me\widgets\Alert;
use app\assets\AdminAsset;
use app\components\functions;
/* @var $this me\components\View */
/* @var $content string */
AdminAsset::register($this);
?>
<!Doctype HTML>
<html lang="<?= Me::$app->language ?>">
    <head>
        <?= $this->head() ?>
    </head>
    <body dir="<?= Me::$app->dir ?>">
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top">
                <div class="navbar-header <?= (isset($_COOKIE['cls']) ? ' h' : '') ?>">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/dashboard/default/index">کالج زبان</a>                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li class="hidden-xs"><a onclick="toggleMenuCookie();"><i class="fa fa-bars"></i></a></li>
                    <li><a style="cursor: default;direction: ltr;">امروز: <?= functions::datestring() ?></a></li>
                    <li><a style="cursor: default;direction: ltr;"><span id="hours" style="width: 20px;display: inline-block;text-align: center;"></span>:<span id="min" style="width: 20px;display: inline-block;text-align: center;"></span>:<span id="sec" style="width: 20px;display: inline-block;text-align: center;"></span></a></li>
                </ul>
                <ul class="nav navbar-top-links navbar-left">
                    <li><a href="/users/profile/index"><i class="fa fa-fw fa-user"></i> پروفایل</a></li>
                    <li><a href="<?= Url::to(['users/auth/signout']) ?>" data-method="post"><i class="fa fa-fw fa-sign-out-alt"></i> خروج</a></li>
                </ul>
            </nav>
            <div class="sidebar <?= (isset($_COOKIE['cls']) ? ' h' : '') ?>" role="navigation">
                <div class="sidebar-nav navbar-collapse collapse">
                    <ul class="nav" id="side-menu">
                        <li><a href="<?= Url::to(['site/default/index']) ?>"><i class="fa fa-fw fa-tachometer-alt"></i> داشبورد</a></li>
                        <li><a href="<?= Url::to(['users/users/index']) ?>"><i class="fa fa-fw fa-users"></i> کاربران</a></li>

<!--                        <li><a href="/dashboard/default/index"><i class="fa fa-fw fa-tachometer-alt"></i> داشبورد</a></li>
<li><a href="/users/users-status/index"><i class="fa fa-fw fa-users"></i> وضعیت کاربران</a></li>
<li><a href="/users/users-groups/index"><i class="fa fa-fw fa-users"></i> گروه کاربری</a></li>
<li><a href="/users/users/index"><i class="fa fa-fw fa-users"></i> کاربران</a></li>
<li><a href="/users/users/create"><i class="fa fa-fw fa-user"></i> ایجاد کاربر جدید</a></li>
<li><a href="/users/clerk/index"><i class="fa fa-fw fa-users"></i> Clerk</a></li>
<li><a href="/users/teacher/index"><i class="fa fa-fw fa-users"></i> Teacher</a></li>
<li><a href="/users/student/index"><i class="fa fa-fw fa-users"></i> Student</a></li>
<li><a href="/site/settings/index"><i class="fa fa-fw fa-cogs"></i> تنظیمات سایت</a></li>
<li><a href="/terms/terms-classes-list/index"><i class="fa fa-fw fa-"></i> کلاس ها</a></li>-->
                    </ul>
                </div>
            </div>
            <div id="page-wrapper" class="<?= (isset($_COOKIE['cls']) ? ' h' : '') ?>">
                <ul class="breadcrumb">
                    <li><a href="<?= Url::to(['site/default/index']) ?>">داشبورد</a></li>
                    <?php
                    if (isset($this->params['breadcrumbs'])) {
                        foreach ($this->params['breadcrumbs'] as $breadcrumb) {
                            $label = '';
                            $url   = '';
                            $class = '';
                            if (is_string($breadcrumb)) {
                                $label = $breadcrumb;
                                $class = 'active';
                            }
                            elseif (is_array($breadcrumb)) {
                                $label = isset($breadcrumb['label']) ? $breadcrumb['label'] : '';
                                $url   = isset($breadcrumb['url']) ? Url::to($breadcrumb['url']) : '';
                                if (!$url) {
                                    $class = 'active';
                                }
                            }
                            if ($label) {
                                echo '<li class="' . $class . '">' . ($url ? '<a href="' . $url . '">' : '') . $label . ($url ? '</a>' : '') . '</li>';
                            }
                        }
                    }
                    ?>
                </ul>
                <div class="container-fluid">
                    <?= Alert::widget() ?>
                    <?= $content ?>
                </div>
            </div>
        </div>
        <footer class="hidden">
            <a href="https://www.hosseinnajafi.ir" title="طراحی سایت حرفه ای">طراحی شده توسط</a> حسین نجفی<br/>
            <a href="https://me.hosseinnajafi.ir" title="ME PHP Framework">قدرت گرفته از قالب کاری</a> ME
        </footer>
        <?= $this->body() ?>
    </body>
</html>