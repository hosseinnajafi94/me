<?php
namespace me\components;
class UrlManager extends Component {
    /**
     * @var string
     */
    public $defaultModule     = 'site';
    /**
     * @var string
     */
    public $defaultController = 'default';
    /**
     * @var string
     */
    public $defaultAction     = 'index';
    /**
     * @var array
     */
    public $map               = [];
    /**
     * @param Request $request
     * @return array [$route, $params]
     */
    public function parseRequest(Request $request): array {
        $pathInfo = trim($request->pathInfo, '/');
        if (isset($this->map[$pathInfo])) {
            return [$this->map[$pathInfo], $request->get()];
        }
        $items         = explode('/', $pathInfo);
        $routeItems    = [];
        $routeItems[0] = (!isset($items[0]) || $items[0] == '' ? $this->defaultModule : $items[0]);
        $routeItems[1] = (!isset($items[1]) || $items[1] == '' ? $this->defaultController : $items[1]);
        $routeItems[2] = (!isset($items[2]) || $items[2] == '' ? $this->defaultAction : $items[2]);
        $route         = implode('/', $routeItems);
        return [$route, $request->get()];
    }
}