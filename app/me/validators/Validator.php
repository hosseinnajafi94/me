<?php
namespace me\validators;
use Me;
use me\components\View;
use me\components\Model;
use me\components\Component;
class Validator extends Component {
    public static $builtInValidators = [
        'boolean'  => 'me\validators\BooleanValidator',
        'captcha'  => 'me\captcha\CaptchaValidator',
        'compare'  => 'me\validators\CompareValidator',
//        'date'     => 'me\validators\DateValidator',
//        'datetime' => ['class' => 'me\validators\DateValidator', 'type' => DateValidator::TYPE_DATETIME],
//        'time'     => ['class' => 'me\validators\DateValidator', 'type' => DateValidator::TYPE_TIME],
        'default'  => 'me\validators\DefaultValueValidator',
        'double'   => 'me\validators\NumberValidator',
        'each'     => 'me\validators\EachValidator',
        'email'    => 'me\validators\EmailValidator',
        'exist'    => 'me\validators\ExistValidator',
        'file'     => 'me\validators\FileValidator',
        'filter'   => 'me\validators\FilterValidator',
        'image'    => 'me\validators\ImageValidator',
        'in'       => 'me\validators\RangeValidator',
        'integer'  => ['class' => 'me\validators\NumberValidator', 'integerOnly' => true],
        'match'    => 'me\validators\RegularExpressionValidator',
        'number'   => 'me\validators\NumberValidator',
        'required' => 'me\validators\RequiredValidator',
        'safe'     => 'me\validators\SafeValidator',
        'string'   => 'me\validators\StringValidator',
        'trim'     => ['class' => 'me\validators\FilterValidator', 'filter' => 'trim', 'skipOnArray' => true],
        'unique'   => 'me\validators\UniqueValidator',
        'url'      => 'me\validators\UrlValidator',
        'ip'       => 'me\validators\IpValidator',
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
            $params['class']  = __NAMESPACE__ . '\InlineValidator';
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