<?php
namespace me\validators;
use Me;
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
    public $skipOnError              = true;
    public $skipOnEmpty              = true;
    public $enableClientValidation   = true;
    public $isEmpty;
    public $when;
    public $whenClient;
    public static function createValidator(string $name, Model $model, array $attributes = [], array $options = []) {
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
    public function validateAttributes(Model $model, array $attributes = null) {
        $attributes = $this->getValidationAttributes($attributes);
        foreach ($attributes as $attribute) {
            $skip = $this->skipOnError && $model->hasErrors($attribute) || $this->skipOnEmpty && $this->isEmpty($model->$attribute);
            if (!$skip) {
                if ($this->when === null || call_user_func($this->when, $model, $attribute)) {
                    $this->validateAttribute($model, $attribute);
                }
            }
        }
    }
    public function validateAttribute(Model $model, string $attribute) {
        $result = $this->validateValue($model->$attribute);
        if (is_array($result) && !empty($result)) {
            $this->addError($model, $attribute, $result[0], $result[1]);
        }
    }
    public function getValidationAttributes($attributes = null) {
        if ($attributes === null) {
            return $this->getAttributeNames();
        }
        if (is_string($attributes)) {
            $attributes = [$attributes];
        }
        $newAttributes  = [];
        $attributeNames = $this->getAttributeNames();
        foreach ($attributes as $attribute) {
            if (in_array($attribute, $attributeNames, true)) {
                $newAttributes[] = $attribute;
            }
        }
        return $newAttributes;
    }
    public function getAttributeNames() {
        return array_map(function ($attribute) {
            return ltrim($attribute, '!');
        }, $this->attributes);
    }
    public function isActive($scenario) {
        return !in_array($scenario, $this->except, true) && (empty($this->on) || in_array($scenario, $this->on, true));
    }
    public function isEmpty($value) {
        if ($this->isEmpty !== null) {
            return call_user_func($this->isEmpty, $value);
        }
        return $value === null || $value === [] || $value === '';
    }
    public function validateValue($value) {
        return null;
    }
    public function formatMessage($message, $params) {
        return Me::t('site', $message, $params);
    }
    /**
     * @param Model $model Model
     * @param string $attribute Attribute Name
     * @param string $message Message
     * @param array $params Parameters
     * @return void
     */
    public function addError(Model $model, string $attribute, string $message, array $params = []) {
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
    public function clientValidateAttribute($model, $attribute, $view) {
        
    }
}