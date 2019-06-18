<?php
namespace me\components;
use ReflectionClass;
use ReflectionProperty;
use me\validators\Validator;
use me\validators\RequiredValidator;
class Model extends Component {
    const SCENARIO_DEFAULT   = 'default';
    private $_scenario = self::SCENARIO_DEFAULT;
    private $_validators;
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
     * @param array $data
     * @param string $formName
     * @return bool
     */
    public function load(array $data = [], string $formName = null): bool {
        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope === '' && !empty($data) && is_array($data)) {
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
        foreach ($this->activeValidators($attribute) as $validator) {
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
     * Returns the first error of the specified attribute.
     * @param string $attribute attribute name.
     * @return string the error message. Null is returned if no error.
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
                $attributes = (array) $rule[0];
                $name = $rule[1];
                $params = array_slice($rule, 2);
                $validators[] = Validator::createValidator($this, $name, $attributes, $params);
            }
        }
        return $validators;
    }
    /**
     * @return Validator[] Validators
     */
    public function getValidators() {
        if ($this->_validators === null) {
            $this->_validators = $this->createValidators();
        }
        return $this->_validators;
    }
    /**
     * @return string
     */
    public function getScenario() {
        return $this->_scenario;
    }
    /**
     * @param string $value
     * @return void
     */
    public function setScenario($value) {
        $this->_scenario = $value;
    }
    /**
     * 
     */
    public function scenarios() {
        $scenarios = [self::SCENARIO_DEFAULT => []];
        foreach ($this->getValidators() as $validator) {
            /* @var $validator Validator */
            foreach ($validator->on as $scenario) {
                $scenarios[$scenario] = [];
            }
            foreach ($validator->except as $scenario) {
                $scenarios[$scenario] = [];
            }
        }
        $names = array_keys($scenarios);
        foreach ($this->getValidators() as $validator) {
            /* @var $validator Validator */
            if (empty($validator->on) && empty($validator->except)) {
                foreach ($names as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            }
            elseif (empty($validator->on)) {
                foreach ($names as $name) {
                    if (!in_array($name, $validator->except, true)) {
                        foreach ($validator->attributes as $attribute) {
                            $scenarios[$name][$attribute] = true;
                        }
                    }
                }
            }
            else {
                foreach ($validator->on as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            }
        }
        foreach ($scenarios as $scenario => $attributes) {
            if (!empty($attributes)) {
                $scenarios[$scenario] = array_keys($attributes);
            }
        }
        return $scenarios;
    }
    /**
     * 
     */
    public function activeAttributes() {
        $scenario  = $this->getScenario();
        $scenarios = $this->scenarios();
        if (!isset($scenarios[$scenario])) {
            return [];
        }
        $attributes = array_keys(array_flip($scenarios[$scenario]));
        foreach ($attributes as $i => $attribute) {
            if ($attribute[0] === '!') {
                $attributes[$i] = substr($attribute, 1);
            }
        }
        return $attributes;
    }
    /**
     * 
     */
    public function activeValidators($attribute = null) {
        $activeAttributes = $this->activeAttributes();
        if ($attribute !== null && !in_array($attribute, $activeAttributes, true)) {
            return [];
        }
        $scenario   = $this->getScenario();
        $validators = [];
        foreach ($this->getValidators() as $validator) {
            /* @var $validator Validator */
            if ($attribute === null) {
                $validatorAttributes = $validator->getValidationAttributes($activeAttributes);
                $attributeValid      = !empty($validatorAttributes);
            }
            else {
                $attributeValid = in_array($attribute, $validator->getValidationAttributes($attribute), true);
            }
            if ($attributeValid && $validator->isActive($scenario)) {
                $validators[] = $validator;
            }
        }
        return $validators;
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
        $scenarios = $this->scenarios();
        $scenario  = $this->getScenario();
        if (!isset($scenarios[$scenario])) {
            return false;
            //throw new InvalidArgumentException("Unknown scenario: $scenario");
        }
        foreach ($this->activeValidators() as $validator) {
            /* @var $validator Validator */
            $validator->validateAttributes($this);
        }
        return !$this->hasErrors();
    }
}