<?php
namespace me\components;
class Response extends Component {
    public $code;
    public $data;
    public function send() {
        http_response_code($this->code);
        echo $this->data;
    }
}