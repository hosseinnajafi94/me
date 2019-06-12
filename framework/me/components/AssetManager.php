<?php
namespace me\components;
use Me;
class AssetManager extends Component {
    public $bundles;
    public function getBundle($name) {
        if (!isset($this->bundles[$name])) {
            $bundle = Me::createObject(['class' => $name]);
            $bundle->publish();
            $this->bundles[$name] = $bundle;
        }
        return $this->bundles[$name];
    }
}