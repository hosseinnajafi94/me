<?php
namespace me\components;
use Me;
use ReflectionClass;
use me\exceptions\NotFound;
/**
 * @property-read string $basePath
 * @property-read string $viewPath
 */
class Module extends Component {
    /**
     * @var string
     */
    public $id;
    /**
     * @var Controller
     */
    public $controller;
    /**
     * @var string
     */
    public $controllerNamespace;
    /**
     * @var string
     */
    private $_basePath;
    /**
     * @return string
     */
    private $_viewPath;
    /**
     * @return string
     */
    private $_tFile;
    /**
     * @return array
     */
    private $_t;
    /**
     * @param string $route
     * @return array|null
     */
    public function createController($route) {
        list($id, $route) = explode('/', $route, 2);
        $controllerName = str_replace(' ', '', ucwords(str_replace('-', ' ', $id)));
        $className      = $this->controllerNamespace . '\\' . $controllerName . 'Controller';
        if (!class_exists($className) || !is_subclass_of($className, 'me\components\Controller')) {
            throw new NotFound("Controller { <b>$id</b> } Not Found");
        }
        $controller = Me::createObject([
                    'id'     => $id,
                    'module' => $this,
                    'class'  => $className,
        ]);
        return [$controller, $route];
    }
    /**
     * @return string
     */
    public function getBasePath() {
        if ($this->_basePath === null) {
            $class           = new ReflectionClass($this);
            $this->_basePath = dirname($class->getFileName());
        }
        return $this->_basePath;
    }
    /**
     * @return string
     */
    public function getViewPath() {
        if ($this->_viewPath === null) {
            $this->_viewPath = $this->basePath
                    . DIRECTORY_SEPARATOR
                    . 'views';
        }
        return $this->_viewPath;
    }
    /**
     * @return string
     */
    private function getTFile() {
        if ($this->_tFile === null) {
            $this->_tFile = $this->basePath
                    . DIRECTORY_SEPARATOR
                    . 'translations'
                    . DIRECTORY_SEPARATOR
                    . Me::$app->language
                    . DIRECTORY_SEPARATOR
                    . $this->id . '.php';
        }
        return $this->_tFile;
    }
    /**
     * @param string $text
     * @param array $replaces
     * @return string
     */
    public function translate($text, $replaces = []) {
        if ($this->_t === null && is_file($this->getTFile())) {
            $this->_t = include $this->getTFile();
        }
        if (isset($this->_t[$text])) {
            $text = $this->_t[$text];
        }
        if (!empty($replaces)) {
            $search  = [];
            $replace = array_values($replaces);
            foreach ($replaces as $key => $value) {
                $search[] = '{' . $key . '}';
            }
            $text = str_replace($search, $replace, $text);
        }
        return $text;
    }
}