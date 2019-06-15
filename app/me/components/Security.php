<?php
namespace me\components;
class Security extends Component {
    public function generatePasswordHash($password) {
        return md5($password);
    }
    public function validatePassword($hash, $password) {
        return $hash === md5($password);
    }
}