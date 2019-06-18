<?php
namespace me\components;
use Me;
class Component {
    /**
     * 
     */
    public function __construct($config = []) {
        if (!empty($config)) {
            Me::configure($this, $config);
        }
        $this->init();
    }
    /**
     * 
     */
    public function init() {
        
    }
    /**
     * 
     */
    public function __get($name) {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        else if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }
    /**
     * 
     */
    public function __set($name, $value) {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
        else if (property_exists($this, $name)) {
            $this->$name = $value;
        }
        return;
    }
    /**
     * @param string $name Method Name
     * @return bool
     */
    public function hasMethod(string $name): bool {
        return method_exists($this, $name);
    }
}