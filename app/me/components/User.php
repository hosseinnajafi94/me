<?php
namespace me\components;
use Me;
/**
 * @property-read IdentityInterface $identity
 * @property-read bool $isGuest
 */
class User extends Component {
    /**
     * @var IdentityInterface
     */
    private $_identity       = false;
    /**
     * @var string
     */
    public $identityClass    = 'me\components\Identity';
    /**
     * @var array
     */
    public $session          = [
        'enable' => true,
        'expire' => 0,
        'names'  => [
            'id'     => '_me_id_',
            'expire' => '_me_expire_',
        ]
    ];
    /**
     * @var bool
     */
    public $enableAutoLogin  = true;
    /**
     * @var array
     */
    public $cookie           = [
        'names' => [
            'id' => '_me_idntity',
        ]
    ];
    /**
     * @param UserInterface $user
     * @param int $expire
     * @return bool
     */
    public function signin(IdentityInterface $user = null, int $expire = 0): bool {
        $this->switchIdentity($user, $expire);
        return !$this->getIsGuest();
    }
    public function signout(bool $destroySession = true): bool {
        $identity = $this->getIdentity();
        if ($identity !== null) {
            $this->switchIdentity(null);
            if ($destroySession && $this->session['enable']) {
                Me::$app->getSession()->destroy();
            }
        }
        return $this->getIsGuest();
    }
    public function getIdentity(bool $autoRenew = true) {
        if ($this->_identity === false) {
            if ($this->session['enable'] && $autoRenew) {
                $this->_identity = null;
                $this->renewAuthStatus();
            }
            else {
                return null;
            }
        }
        return $this->_identity;
    }
    public function getIsGuest() {
        return $this->getIdentity() === null;
    }
    protected function switchIdentity(IdentityInterface $user = null, int $expire = 0) {
        $this->setIdentity($user);
        if (!$this->session['enable']) {
            return;
        }

        $cookie = Me::$app->getCookie();
        $cookie->remove($this->cookie['names']['id']);

        $session = Me::$app->getSession();
        $session->remove($this->session['names']['id']);
        $session->remove($this->session['names']['expire']);

        if ($user) {
            $session->set($this->session['names']['id'], $user->getId());
            if ($this->session['expire'] > 0) {
                $session->set($this->session['names']['expire'], time() + $this->session['expire']);
            }
            if ($this->enableAutoLogin && $expire > 0) {
                $value = json_encode([$user->getId(), $expire]);
                $cookie->set($this->cookie['names']['id'], $value, time() + $expire);
            }
        }
    }
    protected function setIdentity(IdentityInterface $user = null) {
        $this->_identity = $user;
    }
    protected function renewAuthStatus() {
        $session  = Me::$app->getSession();
        $id       = intval($session->get($this->session['names']['id']));
        $identity = null;
        if ($id > 0) {
            /* @var $class IdentityInterface */
            $class    = $this->identityClass;
            $identity = $class::findIdentity(intval($id));
        }
        $this->setIdentity($identity);
        if ($identity !== null && $this->session['expire'] > 0) {
            // update session expire or logout
            $expire = intval($session->get($this->session['names']['expire']));
            if ($expire !== null && $expire < time()) {
                $this->signout(false);
            }
            else {
                $session->set($this->session['names']['expire'], time() + $this->session['expire']);
            }
        }
        if ($this->enableAutoLogin) {
            if ($this->getIsGuest()) {
                $this->loginByCookie();
            }
            else {
                $this->renewIdentityCookie();
            }
        }
    }
    protected function loginByCookie() {
        $cookie = Me::$app->getCookie();
        $name   = $this->cookie['names']['id'];
        $value  = $cookie->get($name);
        if ($value === null || empty($value)) {
            $this->switchIdentity(null);
        }
        else {
            $data = json_decode($value, true);
            if (is_array($data) && count($data) == 2) {
                list($id, $expire) = $data;
                $class    = $this->identityClass;
                $identity = $class::findIdentity(intval($id));
                $this->switchIdentity($identity, $expire);
            }
            else {
                $this->switchIdentity(null);
            }
        }
    }
    protected function renewIdentityCookie() {
        $cookie = Me::$app->getCookie();
        $name   = $this->cookie['names']['id'];
        $value  = $cookie->get($name);
        if ($value !== null) {
            $data = json_decode($value, true);
            if (is_array($data) && isset($data[1]) && is_numeric($data[1])) {
                $expire = time() + intval($data[1]);
                $cookie->set($name, $value, $expire);
            }
        }
    }
}