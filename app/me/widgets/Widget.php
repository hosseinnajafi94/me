<?php
namespace me\widgets;
use Me;
use me\components\Component;
class Widget extends Component {
    public static $counter      = 0;
    public static $autoIdPrefix = 'w';
    public static $stack        = [];
    public $_id;
    public static function begin($config = []) {
        $config['class'] = get_called_class();
        /* @var $widget Widget */
        $widget          = Me::createObject($config);
        self::$stack[]   = $widget;
        return $widget;
    }
    public static function end() {
        if (!empty(self::$stack)) {
            $widget = array_pop(self::$stack);
            if (get_class($widget) === get_called_class()) {
                /* @var $widget Widget */
                $result = $widget->run();
                echo $result;
            }
        }
    }
    public function run() {
        return '';
    }
    public function getId($autoGenerate = true) {
        if ($autoGenerate && $this->_id === null) {
            $this->_id = static::$autoIdPrefix . static::$counter++;
        }
        return $this->_id;
    }
    public static function widget() {
        
    }
}