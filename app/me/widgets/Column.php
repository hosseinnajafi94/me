<?php
namespace me\widgets;
use me\components\Model;
class Column extends Widget {
    /**
     * @var Model
     */
    public $temp;
    /**
     * @var string
     */
    public $attribute;
    /**
     * @var \me\data\ActiveDataProvider
     */
    public $data;
    /**
     * @var \Closure
     */
    public $value;
    /**
     * @return string Title
     */
    public function title() {
        return $this->temp->attributeLabel($this->attribute);
    }
    /**
     * @return string Value
     */
    public function value(Model $model) {
        if ($this->value instanceof \Closure) {
            return call_user_func_array($this->value, [$model]);
        }
        $attribute = $this->attribute;
        return $model->$attribute;
    }
}