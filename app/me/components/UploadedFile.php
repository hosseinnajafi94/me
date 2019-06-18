<?php
namespace me\components;
use me\components\Model;
class UploadedFile extends Component {
    public $multiple = false;
    public $file;
    public $name;
    public $type;
    public $tmp_name;
    public $error;
    public $size;
    public static function getFile(Model $model, string $attribute) {
        $formname = $model->formName();
        if (!isset($_FILES[$formname])) {
            return null;
        }
        elseif (is_string($_FILES[$formname]['name'])) {
            return $_FILES[$formname];
        }
        elseif (is_array($_FILES[$formname]['name']) && isset($_FILES[$formname]['name'][$attribute])) {
            $name     = $_FILES[$formname]['name'][$attribute];
            $type     = $_FILES[$formname]['type'][$attribute];
            $tmp_name = $_FILES[$formname]['tmp_name'][$attribute];
            $error    = $_FILES[$formname]['error'][$attribute];
            $size     = $_FILES[$formname]['size'][$attribute];
            return [
                'name'     => $name,
                'type'     => $type,
                'tmp_name' => $tmp_name,
                'error'    => $error,
                'size'     => $size,
            ];
        }
    }
    public static function getFiles(Model $model, string $attribute) {
        $formname = $model->formName();
        if (!isset($_FILES[$formname]) || !is_array($_FILES[$formname]['name']) || count($_FILES[$formname]['name']) === 0) {
            return null;
        }
        return $_FILES[$formname];
    }
    public function init() {
        parent::init();
        if ($this->file === null || $this->multiple && !is_array($this->file['name']) || !$this->multiple && is_array($this->file['name'])) {
            return;
        }
        $this->name     = $this->file['name'];
        $this->tmp_name = $this->file['tmp_name'];
        $this->error    = $this->file['error'];
        $this->size     = $this->file['size'];
        if ($this->multiple) {
            $types = [];
            foreach ($this->name as $name) {
                $types[] = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            }
            $this->type = $types;
        }
        else {
            $this->type = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
        }
    }
    public function save($path) {
        if ($this->file === null || $this->multiple && !is_array($this->file['name']) || !$this->multiple && is_array($this->file['name'])) {
            return null;
        }
        if ($this->multiple) {
            foreach ($this->error as $error) {
                if ($error !== UPLOAD_ERR_OK) {
                    return null;
                }
            }
            $allmoved     = true;
            $destinations = [];
            foreach ($this->file['tmp_name'] as $index => $tmpname) {
                $destination = uniqid(time(), true) . '.' . $this->type[$index];
                $moved       = $this->move($tmpname, $path . DIRECTORY_SEPARATOR . $destination);
                if ($moved) {
                    $destinations[] = $destination;
                }
                else {
                    $allmoved = false;
                }
            }
            return $allmoved ? $destinations : null;
        }
        else {
            if ($this->error !== UPLOAD_ERR_OK) {
                return null;
            }
            $destination = uniqid(time(), true) . '.' . $this->type;
            $moved       = $this->move($this->tmp_name, $path . DIRECTORY_SEPARATOR . $destination);
            return $moved ? $destination : null;
        }
    }
    protected function move($filename, $destination) {
        return move_uploaded_file($filename, $destination);
    }
}