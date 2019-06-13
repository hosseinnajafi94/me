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
     * @return string Title
     */
    public function title() {
        return $this->temp->attributeLabel($this->attribute);
    }
    /**
     * @return string Value
     */
    public function load(Model $model) {
        $attribute = $this->attribute;
        return $model->$attribute;
    }
}