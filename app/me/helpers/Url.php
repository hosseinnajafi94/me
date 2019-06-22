<?php
namespace me\helpers;
use Me;
class Url extends Helper {
    public static function home(): string {
        
    }
    public static function to(array $url = []): string {
        $path   = trim(ArrayHelper::Remove($url, 0), '/');
        $params = http_build_query($url);
        $key    = array_search($path, Me::$app->urlManager->map);
        if ($key === false) {
            $items = explode('/', $path);
            if (count($items) === 1) {
                $items = [Me::$app->module->id, Me::$app->module->controller->id, $items[0]];
            }
            elseif (count($items) === 2) {
                $items = [Me::$app->module->id, $items[0], $items[1]];
            }
            if ($items[1] === 'default' && $items[2] === 'index') {
                $items = [$items[0]];
            }
            $path = implode('/', $items);
        }
        else {
            $path = $key;
        }
        return Me::getAlias('@web/' . $path . ($params ? '?' . $params : ''));
    }
}