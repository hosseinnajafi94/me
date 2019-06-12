<?php
$modules     = [];
$modulesName = scandir(Me::getAlias('@app/modules'));
foreach ($modulesName as $moduleName) {
    $modules[$moduleName] = 'app\modules\\' . $moduleName . '\Module';
}
//$modules['site']  = 'app\modules\site\Module';
//$modules['users'] = 'app\modules\users\Module';
return $modules;
