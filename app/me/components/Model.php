<?php
namespace me\components;
use ReflectionClass;
use ReflectionProperty;
class Model extends Component {
    protected $_attributes = [];
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
    public static function attributes() {
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
     * @param sreing $name
     * @return bool
     */
    public function hasAttribute(string $name) {
        return isset($this->_attributes[$name]) || in_array($name, $this->attributes(), true);
    }
    /**
     * 
     */
    public function __get($name) {
        if (isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        }
        if ($this->hasAttribute($name)) {
            return null;
        }
        return parent::__get($name);
    }
    /**
     * 
     */
    public function __set($name, $value) {
        if ($this->hasAttribute($name)) {
            $this->_attributes[$name] = $value;
        }
        else {
            parent::__set($name, $value);
        }
    }
    /**
     * @param array $postParams
     * @param string $formName
     * @return bool
     */
    public function load(array $postParams = [], $formName = null) {
        if ($formName === null) {
            $formName = $this->formName();
        }
        if (isset($postParams[$formName]) && is_array($postParams[$formName])) {
            $loaded = true;
            $attributes = $this->attributes();
            foreach ($attributes as $attribute) {
                if (isset($postParams[$formName][$attribute])) {
                    $this->$attribute = $postParams[$formName][$attribute];
                }
                else {
                    $loaded = false;
                }
            }
            return $loaded;
        }
        return false;
    }
    /**
     * @return bool
     */
    public function isAttributeRequired($attribute) {
        return false;
    }
    public function getFirstError($attribute) {
        return null;
    }
    public function addError($attribute, $error) {
        return null;
    }
}