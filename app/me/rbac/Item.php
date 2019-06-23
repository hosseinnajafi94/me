<?php
namespace me\rbac;
use me\components\Component;
class Item extends Component {
    const TYPE_ROLE       = 1;
    const TYPE_PERMISSION = 2;
    public $id;
    public $name;
    public $type;
}