<?php
namespace me\widgets;
use me\components\Model;
class SerialNumberColumn extends Column {
    public static $counter = 1;
    public function init() {
        parent::init();
        $size            = $this->data->pagination['size'];
        $page            = $this->data->page;
        static::$counter = (($page * $size) + 1);
    }
    public function title() {
        return '#';
    }
    public function value(Model $model) {
        return static::$counter++;
    }
}