<?php
namespace me\validators;
use Me;
use me\components\View;
use me\components\Model;
use me\components\Component;
class Validator extends Component {
    public static $builtInValidators = [
        'required' => ['class' => 'me\validators\RequiredValidator'],
        'string'   => ['class' => 'me\validators\StringValidator'],
        'number'   => ['class' => 'me\validators\NumberValidator'],
        'double'   => ['class' => 'me\validators\NumberValidator'],
        'integer'  => ['class' => 'me\validators\NumberValidator', 'integerOnly' => true],
        'file'     => ['class' => 'me\validators\FileValidator'],
        'inline'   => ['class' => 'me\validators\InlineValidator'],
//        'boolean'  => ['class' => 'me\validators\BooleanValidator'],
//        'captcha'  => ['class' => 'me\captcha\CaptchaValidator'],
//        'compare'  => ['class' => 'me\validators\CompareValidator'],
//        'date'     => ['class' => 'me\validators\DateValidator'],
//        'datetime' => ['class' => 'me\validators\DateValidator', 'type' => DateValidator::TYPE_DATETIME],
//        'time'     => ['class' => 'me\validators\DateValidator', 'type' => DateValidator::TYPE_TIME],
//        'default'  => ['class' => 'me\validators\DefaultValueValidator'],
//        'each'     => ['class' => 'me\validators\EachValidator'],
//        'email'    => ['class' => 'me\validators\EmailValidator'],
//        'exist'    => ['class' => 'me\validators\ExistValidator'],
//        'filter'   => ['class' => 'me\validators\FilterValidator'],
//        'image'    => ['class' => 'me\validators\ImageValidator'],
//        'in'       => ['class' => 'me\validators\RangeValidator'],
//        'match'    => ['class' => 'me\validators\RegularExpressionValidator'],
//        'safe'     => ['class' => 'me\validators\SafeValidator'],
//        'trim'     => ['class' => 'me\validators\FilterValidator', 'filter' => 'trim', 'skipOnArray' => true],
//        'unique'   => ['class' => 'me\validators\UniqueValidator'],
//        'url'      => ['class' => 'me\validators\UrlValidator'],
//        'ip'       => ['class' => 'me\validators\IpValidator'],
    ];
    public $message;
    public $attributes               = [];
    public $on                       = [];
    public $except                   = [];
    public $enableClientValidation   = true;
    public $when;
    public $whenClient;
    public static function createValidator(Model $model, string $name, array $attributes = [], array $params = []) {
        $params['attributes'] = $attributes;
        if ($name instanceof \Closure || ($model->hasMethod($name) && !isset(static::$builtInValidators[$name]))) {
            $params['class']  = static::$builtInValidators['inline'];
            $params['method'] = $name;
        }
        else {
            if (isset(static::$builtInValidators[$name])) {
                $name = static::$builtInValidators[$name];
            }
            if (is_array($name)) {
                $params = array_merge($name, $params);
            }
            else {
                $params['class'] = $name;
            }
        }
        return Me::createObject($params);
    }
    public function validateValue(Model $model, string $attribute): array {
        return [];
    }
    public function clientValidateAttribute(Model $model, string $attribute, View $view): string {
        return '';
    }
    public function validateAttributes(Model $model) {
        foreach ($this->attributes as $attribute) {
            if ($this->when === null || call_user_func($this->when, $model, $attribute)) {
                $result = $this->validateValue($model, $attribute);
                if (!empty($result)) {
                    list($message, $params) = $result;
                    $this->addError($model, $attribute, $message, $params);
                }
            }
        }
    }
    public function getValidationAttributes($attributes = null) {
        if ($attributes === null) {
            return $this->attributes;
        }
        if (is_string($attributes)) {
            $attributes = [$attributes];
        }
        $newAttributes = [];
        foreach ($attributes as $attribute) {
            if (in_array($attribute, $this->attributes, true)) {
                $newAttributes[] = $attribute;
            }
        }
        return $newAttributes;
    }
    public function isActive($scenario): bool {
        return !in_array($scenario, $this->except, true) && (empty($this->on) || in_array($scenario, $this->on, true));
    }
    protected function isEmpty($value): bool {
        return $value === null || $value === [] || $value === '';
    }
    protected function formatMessage(string $message, array $params = []): string {
        return Me::t('site', $message, $params);
    }
    protected function addError(Model $model, string $attribute, string $message, array $params = []) {
        $params['attribute'] = $model->attributeLabel($attribute);
        if (!isset($params['value'])) {
            $value = $model->$attribute;
            if (is_array($value)) {
                $params['value'] = 'array()';
            }
            elseif (is_object($value) && !method_exists($value, '__toString')) {
                $params['value'] = '(object)';
            }
            else {
                $params['value'] = $value;
            }
        }
        $model->addError($attribute, $this->formatMessage($message, $params));
    }
}