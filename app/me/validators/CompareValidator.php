<?php
namespace me\validators;
use Me;
use me\helpers\Html;
use me\components\Model;
use me\components\View;
use me\assets\ValidationAsset;
class CompareValidator extends Validator {
    const TYPE_STRING = 'string';
    const TYPE_NUMBER = 'number';
    public $type      = self::TYPE_STRING;
    public $operator  = '==';
    public $compareAttribute;
    public $message;
    public function init() {
        parent::init();
        if ($this->message === null) {
            switch ($this->operator) {
                case '==':
                    $this->message = Me::t('site', '{attribute} must be equal to "{compareAttributeLabel}".');
                    break;
                case '===':
                    $this->message = Me::t('site', '{attribute} must be equal to "{compareAttributeLabel}".');
                    break;
                case '!=':
                    $this->message = Me::t('site', '{attribute} must not be equal to "{compareAttributeLabel}".');
                    break;
                case '!==':
                    $this->message = Me::t('site', '{attribute} must not be equal to "{compareAttributeLabel}".');
                    break;
                case '>':
                    $this->message = Me::t('site', '{attribute} must be greater than "{compareAttributeLabel}".');
                    break;
                case '>=':
                    $this->message = Me::t('site', '{attribute} must be greater than or equal to "{compareAttributeLabel}".');
                    break;
                case '<':
                    $this->message = Me::t('site', '{attribute} must be less than "{compareAttributeLabel}".');
                    break;
                case '<=':
                    $this->message = Me::t('site', '{attribute} must be less than or equal to "{compareAttributeLabel}".');
                    break;
            }
        }
    }
    public function validateValue(Model $model, string $attribute): array {
        $attributeLabel        = $model->attributeLabel($attribute);
        $compareAttributeLabel = $model->attributeLabel($this->compareAttribute);
        $compareAttribute      = $this->compareAttribute;
        $compareValue          = $model->$compareAttribute;
        $value                 = $model->$attribute;
        if ($this->type === self::TYPE_NUMBER) {
            $value        = (float) $value;
            $compareValue = (float) $compareValue;
        }
        else {
            $value        = (string) $value;
            $compareValue = (string) $compareValue;
        }
        $valid = false;
        switch ($this->operator) {
            case '==':
                $valid = $value == $compareValue;
            case '===':
                $valid = $value === $compareValue;
            case '!=':
                $valid = $value != $compareValue;
            case '!==':
                $valid = $value !== $compareValue;
            case '>':
                $valid = $value > $compareValue;
            case '>=':
                $valid = $value >= $compareValue;
            case '<':
                $valid = $value < $compareValue;
            case '<=':
                $valid = $value <= $compareValue;
        }
        if (!$valid) {
            return [$this->message, ['attribute' => $attributeLabel, 'compareAttributeLabel' => $compareAttributeLabel]];
        }
        return null;
    }
    public function clientValidateAttribute(Model $model, string $attribute, View $view): string {
        $attributeLabel        = $model->attributeLabel($attribute);
        $compareAttributeLabel = $model->attributeLabel($this->compareAttribute);
        $compareAttribute      = $this->compareAttribute;
        $options                     = [];
        $options['type']             = $this->type;
        $options['operator']         = $this->operator;
        $options['compareAttribute'] = Html::getInputId($model, $compareAttribute);
        $options['message']          = $this->formatMessage($this->message, ['attribute' => $attributeLabel, 'compareAttributeLabel' => $compareAttributeLabel]);
        ValidationAsset::register($view);
        return 'me.validation.compare(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }
}