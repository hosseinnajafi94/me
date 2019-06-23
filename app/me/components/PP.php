<?php
namespace me\components;
use PhpParser;
class PP {
    public function build($name, $params) {
        return call_user_func_array([$this, 'build' . $name], [$params]);
    }
    public static function buildNamespace(array $params = []) {
        $obj = new PhpParser\Node\Stmt\Namespace_(new PhpParser\Node\Name($params['name']));
        if (isset($params['stmts']) && is_array($params['stmts'])) {
            foreach ($params['stmts'] as $stmt) {
                $obj->stmts[] = (new static)->build($stmt['name'], $stmt['params']);
            }
        }
        return $obj;
    }
    public static function buildUse(array $params = []) {
        $obj = new PhpParser\Node\Stmt\Use_([
            new PhpParser\Node\Stmt\UseUse(new PhpParser\Node\Name($params['name']))
        ]);
        return $obj;
    }
    public static function buildClass(array $params = []) {
        $obj = new PhpParser\Node\Stmt\Class_($params['name']);
        if (isset($params['extend'])) {
            $obj->extends = new PhpParser\Node\Name($params['extend']);
        }
        if (isset($params['stmts']) && is_array($params['stmts'])) {
            foreach ($params['stmts'] as $stmt) {
                $obj->stmts[] = (new static)->build($stmt['name'], $stmt['params']);
            }
        }
        return $obj;
    }
    public static function buildMethod(array $params = []) {
        // 1 public
        // 9 public static
        // 2 protected
        // 10 protected static
        // 4 private
        // 12 private static
        $obj = new PhpParser\Node\Stmt\ClassMethod($params['name']);
        if (isset($params['type'])) {
            $obj->type = $params['type'];
        }
        else {
            $obj->type = 1;
        }
        if (isset($params['params']) && is_array($params['params'])) {
            foreach ($params['params'] as $param) {
                $obj->params[] = (new static)->build('param', $param);
            }
        }
        if (isset($params['stmts']) && is_array($params['stmts'])) {
            foreach ($params['stmts'] as $stmt) {
                $obj->stmts[] = (new static)->build($stmt['name'], $stmt['params']);
            }
        }
        return $obj;
    }
    public static function buildParam(array $params = []) {
        $obj = new \PhpParser\Node\Param($params['name']);
        if (isset($params['default'])) {
            $obj->default = (new static)->build($params['default']['name'], $params['default']['params']);
        }
        if (isset($params['type'])) {
            $obj->type = $params['type'];
        }
        return $obj;
    }
    public static function buildString(array $params = []) {
        $obj = new PhpParser\Node\Scalar\String_($params['value']);
        return $obj;
    }
    public static function buildInt(array $params = []) {
        $obj = new PhpParser\Node\Scalar\LNumber($params['value']);
        return $obj;
    }
    public static function buildReturn(array $params = []) {
        $obj = new PhpParser\Node\Stmt\Return_();
        if (isset($params['expr'])) {
            $obj->expr = (new static)->build($params['expr']['name'], $params['expr']['params']);
        }
        return $obj;
    }
    public static function buildMethodcall(array $params = []) {
        $var = (new static)->build($params['var']['name'], $params['var']['params']);
        $obj = new PhpParser\Node\Expr\MethodCall($var, $params['name']);
        if (isset($params['args']) && is_array($params['args'])) {
            foreach ($params['args'] as $arg) {
                $obj->args[] = (new static)->build($arg['name'], $arg['params']);
            }
        }
        return $obj;
    }
    public static function buildVariable(array $params = []) {
        $obj = new PhpParser\Node\Expr\Variable($params['name']);
        return $obj;
    }
    public static function buildArg(array $params = []) {
        $value = (new static)->build($params['value']['name'], $params['value']['params']);
        $obj   = new PhpParser\Node\Arg($value);
        return $obj;
    }
    public static function buildArray(array $params = []) {
        $obj = new PhpParser\Node\Expr\Array_();
        if (isset($params['items']) && is_array($params['items'])) {
            foreach ($params['items'] as $item) {
                $obj->items[] = (new static)->build($item['name'], $item['params']);
            }
        }
        $obj->setAttribute('kind', 2);
        return $obj;
    }
    public static function buildAssign(array $params = []) {
        $var  = (new static)->build($params['var']['name'], $params['var']['params']);
        $expr = (new static)->build($params['expr']['name'], $params['expr']['params']);
        $obj  = new \PhpParser\Node\Expr\Assign($var, $expr);
        return $obj;
    }
    public static function buildStaticCall(array $params = []) {
        $class = (new static)->build($params['class']['name'], $params['class']['params']);
        $obj   = new \PhpParser\Node\Expr\StaticCall($class, $params['name']);
        return $obj;
    }
    public static function buildName(array $params = []) {
        $obj = new PhpParser\Node\Name($params['parts']);
        return $obj;
    }
}