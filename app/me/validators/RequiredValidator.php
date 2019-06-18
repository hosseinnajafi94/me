<?php
namespace me\validators;
use me\components\View;
use me\components\Model;
use me\assets\ValidationAsset;
class RequiredValidator extends Validator {
    public $requiredValue;
    public $strict = false;
    public function init() {
        parent::init();
        if ($this->message === null) {
            if ($this->requiredValue === null) {
                $this->message = $this->formatMessage('{attribute} cannot be blank.');
            }
            else {
                $this->message = $this->formatMessage('{attribute} must be "{requiredValue}".');
            }
        }
    }
    public function validateValue($value): array {
        if ($this->requiredValue === null) {
            if ($this->strict && $value !== null || !$this->strict && !$this->isEmpty(is_string($value) ? trim($value) : $value)) {
                return [];
            }
            return [$this->message, []];
        }
        if (!$this->strict && $value == $this->requiredValue || $this->strict && $value === $this->requiredValue) {
            return [];
        }
        return [$this->message, ['requiredValue' => $this->requiredValue]];
    }
    public function clientValidateAttribute(Model $model, string $attribute, View $view): string {
        $label              = $model->attributeLabel($attribute);
        $options            = [];
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