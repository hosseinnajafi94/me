<?php
namespace me\validators;
use Me;
use me\assets\ValidationAsset;
class RequiredValidator extends Validator {
    public $requiredValue;
    public $strict      = false;
    public $message;
    public function init() {
        parent::init();
        if ($this->message === null) {
            $this->message = $this->requiredValue === null ? Me::t('site', '{attribute} cannot be blank.') : Me::t('site', '{attribute} must be "{requiredValue}".');
        }
    }
    public function validateValue($value) {
        if ($this->requiredValue === null) {
            if ($this->strict && $value !== null || !$this->strict && !$this->isEmpty(is_string($value) ? trim($value) : $value)) {
                return null;
            }
        }
        elseif (!$this->strict && $value == $this->requiredValue || $this->strict && $value === $this->requiredValue) {
            return null;
        }
        if ($this->requiredValue === null) {
            return [$this->message, []];
        }
        return [$this->message, ['requiredValue' => $this->requiredValue]];
    }
    public function clientValidateAttribute($model, $attribute, $view) {
        $label   = $model->attributeLabel($attribute);
        $options = [];
        $options['message'] = $this->formatMessage($this->message, ['attribute' => $label]);
        if ($this->requiredValue !== null) {
            $options['message']       = $this->formatMessage($options['message'], ['requiredValue' => $this->requiredValue]);
            $options['requiredValue'] = $this->requiredValue;
        }
        if ($this->strict) {
            $options['strict'] = 1;
        }
        ValidationAsset::register($view);
        return 'me.validation.required(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }
}