<?php
namespace me\widgets;
use Me;
use me\helpers\Html;
use me\helpers\ArrayHelper;
class ActiveForm extends Widget {
    public static $autoIdPrefix = 'form';
    public $action  = [];
    public $method  = 'post';
    public $options = [];
    public $fieldConfig = [];
    public $fieldClass  = 'me\widgets\ActiveField';
    public $requiredCssClass = 'required';
    public function init() {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        ob_start();
        ob_implicit_flush(false);
    }
    public function run() {
        $content = ob_get_clean();
        $html    = '';
        $html    .= Html::beginForm($this->action, $this->method, $this->options) . "\n";
        $html    .= $content;
        $html    .= Html::endForm() . "\n";
        return $html;
    }
    /**
     * @return ActiveFie Description
     */
    public function field($model, $attribute, $options = []) {
        ArrayHelper::AddIfNotExist($this->fieldConfig, 'class', $this->fieldClass);
        $config = ArrayHelper::Extend($this->fieldConfig, $options, [
                    'model'     => $model,
                    'attribute' => $attribute,
                    'form'      => $this,
        ]);
        return Me::createObject($config);
    }
}