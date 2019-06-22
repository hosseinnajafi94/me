<?php
namespace me\components;
use Me;
use me\exceptions\NotFound;
use me\exceptions\Exceptions;
/**
 * @property string $layoutPath
 * 
 * @property-read AssetManager $assetManager
 * @property-read \me\rbac\AuthManager  $authManager
 * @property-read Cookie       $cookie
 * @property-read \me\db\Db    $db
 * @property-read Request      $request
 * @property-read Response     $response
 * @property-read Security     $security
 * @property-read Session      $session
 * @property-read UrlManager   $urlManager
 * @property-read User         $user
 * @property-read View         $view
 */
class Application extends Component {
    /**
     * @var array
     */
    public $modules          = [];
    /**
     * @var Module
     */
    public $module;
    /**
     * @var string
     */
    public $language         = 'fa-IR';
    /**
     * @var string
     */
    public $charset          = 'UTF-8';
    /**
     * @var string
     */
    public $dir              = 'rtl';
    /**
     * @var string
     */
    public $errorRoute       = 'site/default/error';
    /**
     * @var string
     */
    public $loginRoute       = 'site/default/login';
    /**
     * @var string
     */
    public $layout           = 'admin';
    /**
     * @var array
     */
    private $_coreModules    = [
        'cg' => [
            'class' => 'me\modules\cg\Module'
        ]
    ];
    /**
     * @var array
     */
    private $_coreComponents = [
        'assetManager' => [
            'class' => 'me\components\AssetManager',
        ],
        'authManager' => [
            'class' => 'me\rbac\AuthManager',
        ],
        'cookie'       => [
            'class' => 'me\components\Cookie',
        ],
        'db'           => [
            'class' => 'me\db\Db',
        ],
        'request'      => [
            'class' => 'me\components\Request',
        ],
        'response'     => [
            'class' => 'me\components\Response',
        ],
        'security'     => [
            'class' => 'me\components\Security',
        ],
        'session'      => [
            'class' => 'me\components\Session',
        ],
        'urlManager'   => [
            'class' => 'me\components\UrlManager',
        ],
        'user'         => [
            'class' => 'me\components\User',
        ],
        'view'     => [
            'class' => 'me\components\View',
        ],
    ];
    /**
     * 
     */
    private $_definitions    = [];
    /**
     * 
     */
    private $_components     = [];
    /**
     * 
     */
    private $_layoutPath;
    public function __construct($config = []) {
        if (!isset($config['components'])) {
            $config['components'] = [];
        }
        if (!isset($config['modules'])) {
            $config['modules'] = [];
        }
        $config['components'] = array_replace_recursive($this->_coreComponents, $config['components']);
        $config['modules']    = array_replace_recursive($this->_coreModules, $config['modules']);
        parent::__construct($config);
    }
    /**
     * @return void
     */
    public function init() {
        parent::init();
        Me::$app = $this;
    }
    /**
     * @return void
     */
    public function run() {
        set_error_handler(function ($severity, $message, $file, $line) {
//            $response       = Me::$app->response;
//            $response->code = 500;
//            $response->data = Me::$app->runAction(Me::$app->errorRoute, ['message' => '500 Internal Server Error']);
//            $response->send();
        });
        try {
            $response = $this->handleRequest();
            $response->send();
            //echo '<pre dir="ltr">';
            //var_dump(Me::$loadedFile);
            //echo '</pre>';
        }
        catch (NotFound $exc) {
            $data = '';
            if (ME_DEBUG) {
                $data .= $exc->getMessage() . '<br/>';
                $data .= $exc->getFile() . ':' . $exc->getLine() . '<br/>';
                foreach ($exc->getTrace() as $row) {
                    if (isset($row['file'], $row['line'])) {
                        $data .= $row['file'] . ':' . $row['line'] . '<br/>';
                    }
                }
            }
            else {
                $data .= $this->runAction($this->errorRoute, ['message' => '404 Page Not Found']);
            }
            $response       = $this->response;
            $response->code = 404;
            $response->data = $data;
            $response->send();
        }
        catch (Exceptions $exc) {
            $data = '';
            if (ME_DEBUG) {
                $data .= $exc->getMessage() . '<br/>';
                $data .= $exc->getFile() . ':' . $exc->getLine() . '<br/>';
                foreach ($exc->getTrace() as $row) {
                    if (isset($row['file'], $row['line'])) {
                        $data .= $row['file'] . ':' . $row['line'] . '<br/>';
                    }
                }
            }
            else {
                $data .= $this->runAction($this->errorRoute, ['message' => '500 Internal Server Error']);
            }
            $response       = $this->response;
            $response->code = 500;
            $response->data = $data;
            $response->send();
        }
    }
    /**
     * @return Response
     */
    public function handleRequest() {
        $request = $this->request;
        list($route, $params) = $request->resolve();
        $result  = $this->runAction($route, $params);
        if ($result instanceof Response) {
            return $result;
        }
        $response       = $this->response;
        $response->data = $result;
        return $response;
    }
    /**
     * @param string $route
     * @param array $params
     * @return Response|string
     */
    public function runAction($route, $params) {
        /* @var $module Module */
        /* @var $controller Controller */
        /* @var $actionID string */
        list($module, $route) = $this->createModule($route);
        $this->module       = $module;
        list($controller, $actionID) = $module->createController($route);
        $module->controller = $controller;
        return $controller->runAction($actionID, $params);
    }
    /**
     * @param string $route
     * @return array
     */
    public function getModule($id) {
        if (!isset($this->modules[$id])) {
            return null;
        }
        if (!$this->modules[$id] instanceof Module) {
            if (is_string($this->modules[$id])) {
                $this->modules[$id] = ['class' => $this->modules[$id]];
            }
            if (!isset($this->modules[$id]['id'])) {
                $this->modules[$id]['id'] = $id;
            }
            $this->modules[$id] = Me::createObject($this->modules[$id]);
        }
        return $this->modules[$id];
    }
    public function createModule($route) {
        list($id, $route) = explode('/', $route, 2);
        if (!isset($this->modules[$id])) {
            throw new NotFound("Module { <b>$id</b> } Not Found");
        }
        if (!$this->modules[$id] instanceof Module) {
            if (is_string($this->modules[$id])) {
                $this->modules[$id] = ['class' => $this->modules[$id]];
            }
            if (!isset($this->modules[$id]['id'])) {
                $this->modules[$id]['id'] = $id;
            }
            $this->modules[$id] = Me::createObject($this->modules[$id]);
        }
        return [$this->modules[$id], $route];
    }
    /**
     * 
     */
    public function getLayoutPath() {
        if ($this->_layoutPath === null) {
            $this->_layoutPath = Me::getAlias('@app/layouts');
        }
        return $this->_layoutPath;
    }
    /**
     * 
     */
    public function setLayoutPath($path) {
        $this->_layoutPath = Me::getAlias($path);
    }
    /**
     * 
     */
    public function get($id) {
        if (isset($this->_components[$id])) {
            return $this->_components[$id];
        }
        else if (isset($this->_definitions[$id])) {
            return $this->_components[$id] = Me::createObject($this->_definitions[$id]);
        }
        return null;
    }
    /**
     * 
     */
    public function set($id, $definition = null) {
        unset($this->_components[$id]);
        if ($definition === null) {
            unset($this->_definitions[$id]);
            return;
        }
        $this->_definitions[$id] = $definition;
    }
    /**
     * @param array $components
     * @return void
     */
    public function setComponents($components) {
        foreach ($components as $id => $definition) {
            $this->set($id, $definition);
        }
    }
    /**
     * @return AssetManager
     */
    public function getAssetManager() {
        return $this->get('assetManager');
    }
    /**
     * @return \me\rbac\AuthManager
     */
    public function getAuthManager() {
        return $this->get('authManager');
    }
    /**
     * @return Cookie
     */
    public function getCookie() {
        return $this->get('cookie');
    }
    /**
     * @return \me\db\Db
     */
    public function getDb() {
        return $this->get('db');
    }
    /**
     * @return Request
     */
    public function getRequest() {
        return $this->get('request');
    }
    /**
     * @return Response
     */
    public function getResponse() {
        return $this->get('response');
    }
    /**
     * @return Security
     */
    public function getSecurity() {
        return $this->get('security');
    }
    /**
     * @return Session
     */
    public function getSession() {
        return $this->get('session');
    }
    /**
     * @return UrlManager
     */
    public function getUrlManager() {
        return $this->get('urlManager');
    }
    /**
     * @return User
     */
    public function getUser() {
        return $this->get('user');
    }
    /**
     * @return View
     */
    public function getView() {
        return $this->get('view');
    }
}