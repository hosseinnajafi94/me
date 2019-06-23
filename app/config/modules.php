<?php
$modules      = [];
$translations = ['me' => Me::getAlias('@me/translations')];
$modulesName  = scandir(Me::getAlias('@app/modules'));
foreach ($modulesName as $moduleName) {
    if ($moduleName != '.' && $moduleName != '..') {
        $modules[$moduleName]      = 'app\modules\\' . $moduleName . '\Module';
        $translations[$moduleName] = Me::getAlias('@app/modules/' . $moduleName . '/translations');
    }
}
//$modules['site']  = 'app\modules\site\Module';
//$modules['users'] = 'app\modules\users\Module';
return [$modules, $translations];
