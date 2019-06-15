<?php
namespace me\components;
class Cookie extends Component {
    public $expire   = 0;
    public $path     = '/';
    public $sameSite = 'strict';
    public $domain   = '';
    public $secure   = false;
    public $httponly = true;
    public function get(string $key, string $defaultValue = null): string {
        $value = filter_input(INPUT_COOKIE, $key);
        if ($value === null || $value === false) {
            return (string) $defaultValue;
        }
        return (string) $value;
    }
    public function set(string $key, string $value, int $expire = 0): bool {
        return setcookie($key, $value, $expire, $this->path, $this->domain, $this->secure, $this->httponly);
    }
    public function remove(string $key) {
        unset($_COOKIE[$key]);
        setcookie($key, null, -1);
    }
}