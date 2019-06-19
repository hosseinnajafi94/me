<?php
namespace me\components;
use ReflectionClass;
use ReflectionProperty;
use me\validators\Validator;
use me\validators\RequiredValidator;
use me\validators\FileValidator;
class Model extends Component {
    /**
     * @var Validator[]
     */
    private $_validators;
    /**
     * @var array
     */
    private $_errors;
    /**
     * @return array Rules
     */
    public function rules() {
        return [];
    }
    /**
     * @return array Labels
     */
    public function labels() {
        return [];
    }
    /**
     * @return array Hints
     */
    public function hints() {
        return [];
    }
    /**
     * @return array Attributes Names
     */
    public static function attributes(): array {
        $class = new ReflectionClass(get_called_class());
        $names = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }
        return $names;
    }
    /**
     * @return string Form Name
     */
    public function formName() {
        return basename(get_called_class());
    }
    /**
     * @param string $attribute Attribute Name
     * @return string Attribute Label
     */
    public function attributeLabel(string $attribute) {
        $labels = $this->labels();
        if (isset($labels[$attribute])) {
            return $labels[$attribute];
        }
        return $attribute;
    }
    /**
     * @param string $attribute Attribute Name
     * @return string Attribute Hint
     */
    public function attributeHint(string $attribute) {
        $hints = $this->hints();
        if (isset($hints[$attribute])) {
            return $hints[$attribute];
        }
        return null;
    }
    /**
     * @param array $data
     * @param string $formName
     * @return bool
     */
    public function load(array $data = [], string $formName = null): bool {
        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope === '' && !empty($data)) {
            $this->setAttributes($data);
            return true;
        }
        elseif (isset($data[$scope]) && is_array($data[$scope])) {
            $this->setAttributes($data[$scope]);
            return true;
        }
        return false;
    }
    /**
     * @param array $data
     * @param string $formName
     * @return bool
     */
    public function loadFiles(array $data = [], string $formName = null) {
        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope === '' && !empty($data)) {
            $attributes = $this->activeFileAttributes();
            foreach ($attributes as $attribute) {
                if (isset($data[$attribute]) && !empty($data[$attribute]['name'])) {
                    $this->$attribute = $data[$attribute];
                }
            }
            return true;
        }
        elseif (isset($data[$scope]) && is_array($data[$scope])) {
            $attributes = $this->activeFileAttributes();
            foreach ($attributes as $attribute) {
                if (isset($data[$scope]['name'][$attribute]) && !empty($data[$scope]['name'][$attribute])) {
                    $this->$attribute = [
                        'name'     => $data[$scope]['name'][$attribute],
                        'type'     => $data[$scope]['type'][$attribute],
                        'tmp_name' => $data[$scope]['tmp_name'][$attribute],
                        'error'    => $data[$scope]['error'][$attribute],
                        'size'     => $data[$scope]['size'][$attribute],
                    ];
                }
            }
            return true;
        }
        return false;
    }
    /**
     * @param array $values
     * @return void
     */
    public function setAttributes(array $values) {
        $attributes = array_flip($this->attributes());
        foreach ($values as $name => $value) {
            if (isset($attributes[$name])) {
                $this->$name = $value;
            }
        }
    }
    /**
     * @param string $attribute Attribute Name
     * @return bool
     */
    public function isAttributeRequired(string $attribute) {
        foreach ($this->getValidators($attribute) as $validator) {
            /* @var $validator Validator */
            if ($validator instanceof RequiredValidator && $validator->when === null) {
                return true;
            }
        }
        return false;
    }
    /**
     * 
     */
    public function getFirstErrors() {
        if (empty($this->_errors)) {
            return [];
        }
        $errors = [];
        foreach ($this->_errors as $name => $es) {
            if (!empty($es)) {
                $errors[$name] = reset($es);
            }
        }
        return $errors;
    }
    /**
     * @param string $attribute attribute name
     * @return string|null
     */
    public function getFirstError($attribute) {
        return isset($this->_errors[$attribute]) ? reset($this->_errors[$attribute]) : null;
    }
    /**
     * @param string $attribute Attribute Name
     * @param string $error Error Message
     * @return void
     */
    public function addError($attribute, $error) {
        $this->_errors[$attribute][] = $error;
    }
    /**
     * @param array $items Items
     * @return void
     */
    public function addErrors(array $items) {
        foreach ($items as $attribute => $errors) {
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    $this->addError($attribute, $error);
                }
            }
            else {
                $this->addError($attribute, $errors);
            }
        }
    }
    /**
     * @return Validator[] Validators
     */
    public function createValidators() {
        $validators = [];
        foreach ($this->rules() as $rule) {
            if ($rule instanceof Validator) {
                $validators[] = $rule;
            }
            elseif (is_array($rule) && isset($rule[0], $rule[1])) {
                $attributes   = (array) $rule[0];
                $name         = $rule[1];
                $params       = array_slice($rule, 2);
                $validators[] = Validator::createValidator($this, $name, $attributes, $params);
            }
        }
        return $validators;
    }
    /**
     * @param string|null $attribute
     * @return Validator[]
     */
    public function getValidators($attribute = null) {
        if ($this->_validators === null) {
            $this->_validators = $this->createValidators();
        }
        if ($attribute === null) {
            return $this->_validators;
        }
        /* @var $validators Validator[] */
        $validators = [];
        foreach ($this->_validators as $validator) {
            /* @var $validator Validator */
            if (in_array($attribute, $validator->attributes)) {
                $validators[] = $validator;
            }
        }
        return $validators;
    }
    /**
     * 
     */
    public function activeAttributes() {
        $attributes = [];
        foreach ($this->getValidators() as $validator) {
            foreach ($validator->attributes as $attribute) {
                $attributes[$attribute] = true;
            }
        }
        return array_keys($attributes);
    }
    /**
     * 
     */
    public function activeFileAttributes() {
        $attributes = [];
        foreach ($this->getValidators() as $validator) {
            if ($validator instanceof FileValidator) {
                $attributes = array_merge($attributes, $validator->attributes);
            }
        }
        return $attributes;
    }
    /**
     * 
     */
    public function clearErrors($attribute = null) {
        if ($attribute === null) {
            $this->_errors = [];
        }
        else {
            unset($this->_errors[$attribute]);
        }
    }
    /**
     * 
     */
    public function hasErrors($attribute = null) {
        return $attribute === null ? !empty($this->_errors) : isset($this->_errors[$attribute]);
    }
    /**
     * 
     */
    public function validate(bool $clearErrors = true) {
        if ($clearErrors) {
            $this->clearErrors();
        }
        foreach ($this->getValidators() as $validator) {
            /* @var $validator Validator */
            $validator->validateAttributes($this);
        }
        return !$this->hasErrors();
    }
}