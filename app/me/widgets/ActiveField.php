<?php
namespace me\widgets;
use me\helpers\Html;
use me\helpers\ArrayHelper;
use me\components\Component;
class ActiveField extends Component {
    /**
     * @var \me\components\Model
     */
    public $model;
    /**
     * @var string
     */
    public $attribute;
    /**
     * @var ActiveForm
     */
    public $form;
    /**
     * @var array
     */
    public $options      = ['class' => 'form-group'];
    /**
     * @var string
     */
    public $template     = "{label}{input}{hint}{error}";
    /**
     * @var array
     */
    public $labelOptions = ['class' => 'control-label'];
    /**
     * @var array
     */
    public $inputOptions = ['class' => 'form-control input-sm'];
    /**
     * @var array
     */
    public $hintOptions  = ['class' => 'hint-block'];
    /**
     * @var array
     */
    public $errorOptions = ['class' => 'help-block'];
    /**
     * @var array
     */
    public $parts        = [];
    /**
     * 
     */
    public function __toString() {
        return $this->render();
    }
    protected function render(string $content = null) {
        if ($content === null) {
            if (!isset($this->parts['{label}'])) {
                $this->label();
            }
            if (!isset($this->parts['{input}'])) {
                $this->textInput();
            }
            if (!isset($this->parts['{hint}'])) {
                $this->hint(null);
            }
            if (!isset($this->parts['{error}'])) {
                $this->error();
            }
            $content = strtr($this->template, $this->parts);
        }
        return $this->begin() . $content . $this->end() . "\n";
    }
    protected function begin() {
        $inputID = Html::getInputId($this->model, $this->attribute);
        $options = $this->options;
        $class   = isset($options['class']) ? (array) $options['class'] : [];
        $class[] = "field-$inputID";
        if ($this->model->isAttributeRequired($this->attribute)) {
            $class[] = $this->form->requiredCssClass;
        }
        $options['class'] = implode(' ', $class);
        return '<div' . Html::optionToAttr($options) . '>';
    }
    protected function end() {
        return '</div>';
    }
    public function label(string $label = null, array $options = []) {
        if ($label === false) {
            $this->parts['{label}'] = '';
            return $this;
        }
        $options = ArrayHelper::Extend($this->labelOptions, $options);
        if ($label !== null) {
            $options['label'] = $label;
        }
        $this->parts['{label}'] = Html::activeLabel($this->model, $this->attribute, $options);
        return $this;
    }
    public function hint(string $content = null, array $options = []) {
        if ($content === false) {
            $this->parts['{hint}'] = '';
            return $this;
        }
        $options = ArrayHelper::Extend($this->hintOptions, $options);
        if ($content !== null) {
            $options['hint'] = $content;
        }
        $this->parts['{hint}'] = Html::activeHint($this->model, $this->attribute, $options);
        return $this;
    }
    public function error(array $options = []) {
        if ($options === false) {
            $this->parts['{error}'] = '';
            return $this;
        }
        $options                = ArrayHelper::Extend($this->errorOptions, $options);
        $this->parts['{error}'] = Html::error($this->model, $this->attribute, $options);
        return $this;
    }
    public function textInput(array $options = []) {
        $options                = ArrayHelper::Extend($this->inputOptions, $options);
        $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);
        return $this;
    }
    public function passwordInput(array $options = []) {
        $options                = ArrayHelper::Extend($this->inputOptions, $options);
        $this->parts['{input}'] = Html::activePasswordInput($this->model, $this->attribute, $options);
        return $this;
    }
    public function fileInput(array $options = []) {
        $this->parts['{input}'] = Html::activeFileInput($this->model, $this->attribute, $options);
        return $this;
    }
    public function radioInput(array $options = []) {
        $this->parts['{input}'] = Html::activeRadioInput($this->model, $this->attribute, $options);
        return $this;
    }
    public function checkboxInput(array $options = []) {
        $this->parts['{input}'] = Html::activeCheckboxInput($this->model, $this->attribute, $options);
        return $this;
    }
    public function colorInput(array $options = []) {
        $this->parts['{input}'] = Html::activeColorInput($this->model, $this->attribute, $options);
        return $this;
    }
    public function dropDownListInput(array $items = [], array $options = []) {
        $options                = ArrayHelper::Extend($this->inputOptions, ['prompt' => 'لطفا انتخاب کنید'], $options);
        $this->parts['{input}'] = Html::activeDropDownList($this->model, $this->attribute, $items, $options);
        return $this;
    }
    public function listBoxInput(array $items = [], array $options = []) {
        $options                = ArrayHelper::Extend($this->inputOptions, $options);
        $this->parts['{input}'] = Html::activeListBox($this->model, $this->attribute, $items, $options);
        return $this;
    }
    public function checkboxListInput(array $items = [], array $options = []) {
        $this->parts['{input}'] = Html::activeCheckboxList($this->model, $this->attribute, $items, $options);
        return $this;
    }
    public function radioListInput(array $items = [], array $options = []) {
        $this->parts['{input}'] = Html::activeRadioList($this->model, $this->attribute, $items, $options);
        return $this;
    }
}