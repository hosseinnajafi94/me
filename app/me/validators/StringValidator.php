<?php
namespace me\validators;
use me\components\View;
use me\components\Model;
use me\assets\ValidationAsset;
class StringValidator extends Validator {
    public $min;
    public $max;
    public $length;
    public $tooShort;
    public $tooLong;
    public $notEqual;
    public function init() {
        parent::init();
        if (is_array($this->length)) {
            if (isset($this->length[0])) {
                $this->min = $this->length[0];
            }
            if (isset($this->length[1])) {
                $this->max = $this->length[1];
            }
            $this->length = null;
        }
        if ($this->message === null) {
            $this->message = $this->formatMessage('{attribute} must be a string.');
        }
        if ($this->min !== null && $this->tooShort === null) {
            $this->tooShort = $this->formatMessage('{attribute} should contain at least {min}');
        }
        if ($this->max !== null && $this->tooLong === null) {
            $this->tooLong = $this->formatMessage('{attribute} should contain at most {max}');
        }
        if ($this->length !== null && $this->notEqual === null) {
            $this->notEqual = $this->formatMessage('{attribute} should contain {length}');
        }
    }
    public function validateValue($value): array {
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
        $label   = $model->attributeLabel($attribute);
        $options = ['message' => $this->formatMessage($this->message, ['attribute' => $label])];
        if ($this->min !== null) {
            $options['min']      = $this->min;
            $options['tooShort'] = $this->formatMessage($this->tooShort, ['attribute' => $label, 'min' => $this->min]);
        }
        if ($this->max !== null) {
            $options['max']     = $this->max;
            $options['tooLong'] = $this->formatMessage($this->tooLong, ['attribute' => $label, 'max' => $this->max]);
        }
        if ($this->length !== null) {
            $options['is']       = $this->length;
            $options['notEqual'] = $this->formatMessage($this->notEqual, ['attribute' => $label, 'length' => $this->length]);
        }
        ValidationAsset::register($view);
        return 'me.validation.string(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }
}