<?php
namespace me\validators;
use Me;
use me\components\Model;
use me\assets\ValidationAsset;
class StringValidator extends Validator {
    public $length;
    public $max;
    public $min;
    public $message;
    public $tooShort;
    public $tooLong;
    public $notEqual;
    public $encoding;
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
        if ($this->encoding === null) {
            $this->encoding = Me::$app ? Me::$app->charset : 'UTF-8';
        }
        if ($this->message === null) {
            $this->message = Me::t('site', '{attribute} must be a string.');
        }
        if ($this->min !== null && $this->tooShort === null) {
            $this->tooShort = Me::t('site', '{attribute} should contain at least {min, number} {min, plural, one{character} other{characters}}.');
        }
        if ($this->max !== null && $this->tooLong === null) {
            $this->tooLong = Me::t('site', '{attribute} should contain at most {max, number} {max, plural, one{character} other{characters}}.');
        }
        if ($this->length !== null && $this->notEqual === null) {
            $this->notEqual = Me::t('site', '{attribute} should contain {length, number} {length, plural, one{character} other{characters}}.');
        }
    }
    public function validateAttribute(Model $model, string $attribute) {
        $value = $model->$attribute;
        if (!is_string($value)) {
            $this->addError($model, $attribute, $this->message);
            return;
        }
        $length = mb_strlen($value, $this->encoding);
        if ($this->min !== null && $length < $this->min) {
            $this->addError($model, $attribute, $this->tooShort, ['min' => $this->min]);
        }
        if ($this->max !== null && $length > $this->max) {
            $this->addError($model, $attribute, $this->tooLong, ['max' => $this->max]);
        }
        if ($this->length !== null && $length !== $this->length) {
            $this->addError($model, $attribute, $this->notEqual, ['length' => $this->length]);
        }
    }
    public function validateValue($value) {
        if (!is_string($value)) {
            return [$this->message, []];
        }
        $length = mb_strlen($value, $this->encoding);
        if ($this->min !== null && $length < $this->min) {
            return [$this->tooShort, ['min' => $this->min]];
        }
        if ($this->max !== null && $length > $this->max) {
            return [$this->tooLong, ['max' => $this->max]];
        }
        if ($this->length !== null && $length !== $this->length) {
            return [$this->notEqual, ['length' => $this->length]];
        }

        return null;
    }
    public function clientValidateAttribute($model, $attribute, $view) {
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
        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }
        ValidationAsset::register($view);
        return 'me.validation.string(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }
}