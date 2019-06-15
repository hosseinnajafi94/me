<?php
namespace me\components;
class Session extends Component {
    private $frozenSessionData;
    public function open() {
        if ($this->getIsActive()) {
            return;
        }
        if (ME_DEBUG && !headers_sent()) {
            session_start();
        }
        else {
            @session_start();
        }
    }
    public function close() {
        if ($this->getIsActive()) {
            if (ME_DEBUG && !headers_sent()) {
                session_write_close();
            }
            else {
                @session_write_close();
            }
        }
    }
    public function destroy() {
        if ($this->getIsActive()) {
            $sessionId = $this->getId();
            $this->close();
            $this->setId($sessionId);
            $this->open();
            session_unset();
            session_destroy();
            $this->setId($sessionId);
        }
    }
    public function get($key, $defaultValue = null) {
        $this->open();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }
    public function set($key, $value) {
        $this->open();
        $_SESSION[$key] = $value;
    }
    public function has($key) {
        $this->open();
        return isset($_SESSION[$key]);
    }
    public function remove($key) {
        $this->open();
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $value;
        }
        return null;
    }
    public function removeAll() {
        $this->open();
        foreach (array_keys($_SESSION) as $key) {
            unset($_SESSION[$key]);
        }
    }
    public function regenerateID($deleteOldSession = false) {
        if ($this->getIsActive()) {
            if (ME_DEBUG && !headers_sent()) {
                session_regenerate_id($deleteOldSession);
            }
            else {
                @session_regenerate_id($deleteOldSession);
            }
        }
    }
    public function getId() {
        return session_id();
    }
    public function setId($value) {
        session_id($value);
    }
    public function getIsActive() {
        return session_status() === PHP_SESSION_ACTIVE;
    }
    public function getName() {
        return session_name();
    }
    public function setName($value) {
        $this->freeze();
        session_name($value);
        $this->unfreeze();
    }
    protected function freeze() {
        if ($this->getIsActive()) {
            if (isset($_SESSION)) {
                $this->frozenSessionData = $_SESSION;
            }
            $this->close();
        }
    }
    protected function unfreeze() {
        if (null !== $this->frozenSessionData) {
            if (ME_DEBUG) {
                session_start();
            }
            else {
                @session_start();
            }
            $_SESSION                = $this->frozenSessionData;
            $this->frozenSessionData = null;
        }
    }
}