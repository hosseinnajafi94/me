<?php
//
defined('ME_DEBUG') || define('ME_DEBUG', false);
if (!ME_DEBUG) {
    error_reporting(0);
}
//
define('ME_PATH', __DIR__);
define('APP_PATH', dirname(ME_PATH));
define('ROOT_PATH', dirname(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME')));
define('WEB', dirname(filter_input(INPUT_SERVER, 'PHP_SELF')));
//
class Me {
    /**
     * @var \me\components\Application
     */
    public static $app;
    /**
     * @var array
     */
    public static $classMap   = [];
    /**
     * @var array
     */
    public static $loadedFile = [];
    /**
     * @var me\components\Container
     */
    public static $container;
    /**
     * @var array
     */
    public static $aliases    = [
        '@me'   => ME_PATH,
        '@app'  => APP_PATH,
        '@root' => ROOT_PATH,
        '@web'  => WEB,
    ];
    /**
     * 
     */
    public static function autoload($className) {
        $file = '';
        if (isset(static::$classMap[$className])) {
            $file = static::$classMap[$className];
        }
        else if (substr($className, 0, 3) == 'me\\') {
            $file = ME_PATH . str_replace(['me\\', '\\'], ['\\', '/'], $className) . '.php';
        }
        else if (substr($className, 0, 4) == 'app\\') {
            $file = APP_PATH . str_replace(['app\\', '\\'], ['\\', '/'], $className) . '.php';
        }
        else {
            return;
        }
        if (!is_file($file)) {
            return;
        }
        static::$loadedFile[] = $file;
        include $file;
    }
    /**
     * 
     */
    public static function configure($object, $properties) {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }
        return $object;
    }
    /**
     * 
     */
    public static function createObject($id) {
        return static::$container->build($id);
    }
    /**
     * 
     */
    public static function getAlias($alias) {
        if (substr($alias, 0, 1) !== '@') {
            return $alias;
        }
        $root = $alias;
        $path = '';
        if (($pos  = strpos($alias, '/')) !== false) {
            $root = substr($alias, 0, $pos);
            $path = substr($alias, $pos);
        }
        if (isset(static::$aliases[$root])) {
            return static::getAlias(static::$aliases[$root] . $path);
        }
        return null;
    }
    /**
     * 
     */
    public static function setAlias($alias, $path) {
        static::$aliases[$alias] = $path;
    }
    /**
     * 
     */
    private static $translations = [];
    public static function t($category, $message, $params = []) {
        $app = static::$app;
        if (!isset($app->translations[$category])) {
            return static::formatText($message, $params);
        }
        if (!isset(static::$translations[$category])) {
            $path = static::$app->translations[$category];
            $lang = static::$app->language;
            $file = "$path/$lang/$category.php";
            if (!is_file($file)) {
                return static::formatText($message, $params);
            }
            static::$translations[$category] = include $file;
        }
        $messages = static::$translations[$category];
        if (!isset($messages[$message])) {
            return static::formatText($message, $params);
        }
        return static::formatText($messages[$message], $params);
    }
    private static function formatText(string $text = null, $params = []) {
        if ($text === null) {
            return null;
        }
        if (empty($params)) {
            return $text;
        }
        $search  = [];
        $replace = array_values($params);
        foreach ($params as $key => $value) {
            $search[] = '{' . $key . '}';
        }
        $text = str_replace($search, $replace, $text);
        return $text;
    }
}
//
spl_autoload_register(['Me', 'autoload']);
Me::$container = new me\components\Container();
//
function files($name = null, $defaultValue = null) {
    return Me::$app->request->files($name, $defaultValue);
}
function post($name = null, $defaultValue = null) {
    return Me::$app->request->post($name, $defaultValue);
}
function get($name = null, $defaultValue = null) {
    return Me::$app->request->get($name, $defaultValue);
}
