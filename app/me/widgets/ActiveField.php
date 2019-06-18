<?php
namespace me\widgets;
use me\helpers\Html;
use me\helpers\ArrayHelper;
use me\components\Component;
use me\components\JsExpression;
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
    /**
     * @param string $content
     * @return string
     */
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
    /**
     * @return string
     */
    protected function begin() {
        $clientOptions = $this->getClientOptions();
        if (!empty($clientOptions)) {
            $this->form->attributes[] = $clientOptions;
        }
        $inputID = Html::getInputId($this->model, $this->attribute);
        $options = $this->options;
        $class   = isset($options['class']) ? (array) $options['class'] : [];
        $class[] = "field-$inputID";
        if ($this->model->isAttributeRequired($this->attribute)) {
            $class[] = $this->form->requiredCssClass;
        }
        if ($this->model->hasErrors($this->attribute)) {
            $class[] = $this->form->errorCssClass;
        }
        $options['class'] = implode(' ', $class);
        return '<div' . Html::optionToAttr($options) . '>';
    }
    /**
     * @return string
     */
    protected function end() {
        return '</div>';
    }
    /**
     * @param string $label
     * @param array $options
     * @return self
     */
    public function label(string $label = null, array $options = []): self {
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
    /**
     * @param string $content
     * @param array $options
     * @return self
     */
    public function hint(string $content = null, array $options = []): self {
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
    /**
     * @param array $options
     * @return self
     */
    public function error(array $options = []): self {
        if ($options === false) {
            $this->parts['{error}'] = '';
            return $this;
        }
        $options                = ArrayHelper::Extend($this->errorOptions, $options);
        $this->parts['{error}'] = Html::error($this->model, $this->attribute, $options);
        return $this;
    }
    /**
     * @param array $options
     * @return self
     */
    public function textInput(array $options = []): self {
        $options                = ArrayHelper::Extend($this->inputOptions, $options);
        $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);
        return $this;
    }
    /**
     * @param array $options
     * @return self
     */
    public function passwordInput(array $options = []): self {
        $options                = ArrayHelper::Extend($this->inputOptions, $options);
        $this->parts['{input}'] = Html::activePasswordInput($this->model, $this->attribute, $options);
        return $this;
    }
    /**
     * @param array $options
     * @return self
     */
    public function fileInput(array $options = []): self {
        $this->form->options['enctype'] = 'multipart/form-data';
        $this->parts['{input}']         = Html::activeFileInput($this->model, $this->attribute, $options);
        return $this;
    }
    /**
     * @param array $options
     * @return self
     */
    public function radioInput(array $options = []): self {
        $this->parts['{input}'] = Html::activeRadioInput($this->model, $this->attribute, $options);
        return $this;
    }
    /**
     * @param array $options
     * @return self
     */
    public function checkboxInput(array $options = []): self {
        $this->parts['{input}'] = Html::activeCheckboxInput($this->model, $this->attribute, $options);
        return $this;
    }
    /**
     * @param array $options
     * @return self
     */
    public function colorInput(array $options = []): self {
        $this->parts['{input}'] = Html::activeColorInput($this->model, $this->attribute, $options);
        return $this;
    }
    /**
     * @param array $items
     * @param array $options
     * @return self
     */
    public function dropDownListInput(array $items = [], array $options = []): self {
        $options                = ArrayHelper::Extend($this->inputOptions, ['prompt' => 'لطفا انتخاب کنید'], $options);
        $this->parts['{input}'] = Html::activeDropDownList($this->model, $this->attribute, $items, $options);
        return $this;
    }
    /**
     * @param array $items
     * @param array $options
     * @return self
     */
    public function listBoxInput(array $items = [], array $options = []): self {
        $options                = ArrayHelper::Extend($this->inputOptions, $options);
        $this->parts['{input}'] = Html::activeListBox($this->model, $this->attribute, $items, $options);
        return $this;
    }
    /**
     * @param array $items
     * @param array $options
     * @return self
     */
    public function checkboxListInput(array $items = [], array $options = []): self {
        $this->parts['{input}'] = Html::activeCheckboxList($this->model, $this->attribute, $items, $options);
        return $this;
    }
    /**
     * @param array $items
     * @param array $options
     * @return self
     */
    public function radioListInput(array $items = [], array $options = []): self {
        $this->parts['{input}'] = Html::activeRadioList($this->model, $this->attribute, $items, $options);
        return $this;
    }
    /**
     * @return array
     */
    public function getClientOptions(): array {
        $attribute = $this->attribute;
        if (!in_array($attribute, $this->model->activeAttributes(), true)) {
            return [];
        }
        $validators = [];
        foreach ($this->model->activeValidators($attribute) as $validator) {
            /* @var $validator \me\validators\Validator */
            $js = $validator->clientValidateAttribute($this->model, $attribute, $this->form->getView());
            if ($validator->enableClientValidation && $js) {
                if ($validator->whenClient !== null) {
                    $js = "if (({$validator->whenClient})(attribute, value)) { $js }";
                }
                $validators[] = $js;
            }
        }
        if (empty($validators)) {
            return [];
        }
        $inputID = Html::getInputId($this->model, $this->attribute);
        $options              = [];
        $options['name']      = $attribute;
        $options['container'] = ".field-$inputID";
        $options['input']     = "#$inputID";
        if (!empty($validators)) {
            $options['validate'] = new JsExpression('function (attribute, value, messages, deferred, $form) {' . implode('', $validators) . '}');
        }
        return $options;
    }
}