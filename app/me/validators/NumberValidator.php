<?php
namespace me\validators;
use me\components\View;
use me\components\Model;
use me\components\JsExpression;
use me\helpers\Json;
use me\assets\ValidationAsset;
class NumberValidator extends Validator {
    public $integerOnly    = false;
    public $max;
    public $min;
    public $tooBig;
    public $tooSmall;
    public $integerPattern = '/^\s*[+-]?\d+\s*$/';
    public $numberPattern  = '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';
    public function init() {
        parent::init();
        if ($this->message === null) {
            $this->message = $this->integerOnly ? $this->formatMessage('{attribute} must be an integer.') : $this->formatMessage('{attribute} must be a number.');
        }
        if ($this->min !== null && $this->tooSmall === null) {
            $this->tooSmall = $this->formatMessage('{attribute} must be no less than {min}.');
        }
        if ($this->max !== null && $this->tooBig === null) {
            $this->tooBig = $this->formatMessage('{attribute} must be no greater than {max}.');
        }
    }
    public function validateValue(Model $model, string $attribute): array {
        $value = $model->$attribute;
        if (!is_string($value)) {
            return [$this->message, []];
        }
        $length = mb_strlen($value, 'UTF-8');
        if ($this->min !== null && $length < $this->min) {
            return [$this->tooShort, ['min' => $this->min]];
        }
        if ($this->max !== null && $length > $this->max) {
            return [$this->tooLong, ['max' => $this->max]];
        }
        if ($this->length !== null && $length !== $this->length) {
            return [$this->notEqual, ['length' => $this->length]];
        }
        return [];
    }
    public function clientValidateAttribute(Model $model, string $attribute, View $view): string {
        $label = $model->attributeLabel($attribute);
        $options = [
            'pattern' => new JsExpression($this->integerOnly ? $this->integerPattern : $this->numberPattern),
            'message' => $this->formatMessage($this->message, [
                'attribute' => $label,
            ]),
        ];
        if ($this->min !== null) {
            $options['min'] = is_string($this->min) ? (float) $this->min : $this->min;
            $options['tooSmall'] = $this->formatMessage($this->tooSmall, [
                'attribute' => $label,
                'min' => $this->min,
            ]);
        }
        if ($this->max !== null) {
            $options['max'] = is_string($this->max) ? (float) $this->max : $this->max;
            $options['tooBig'] = $this->formatMessage($this->tooBig, [
                'attribute' => $label,
                'max' => $this->max,
            ]);
        }
        ValidationAsset::register($view);
        return 'me.validation.number(value, messages, ' . Json::encode($options) . ');';
    }
}