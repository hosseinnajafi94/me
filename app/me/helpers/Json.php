<?php
namespace me\helpers;
use me\components\JsExpression;
class Json extends Helper {
    public static function encode($value, $options = 320) {
        $expressions = [];
        $value       = static::processData($value, $expressions, uniqid('', true));
        $json        = json_encode($value, $options);
        return $expressions === [] ? $json : strtr($json, $expressions);
    }
    protected static function processData($data, &$expressions, $expPrefix) {
        if (is_object($data)) {
            if ($data instanceof JsExpression) {
                $token                           = "!{[$expPrefix=" . count($expressions) . ']}!';
                $expressions['"' . $token . '"'] = $data->expression;
                return $token;
            }
            elseif ($data instanceof \JsonSerializable) {
                return static::processData($data->jsonSerialize(), $expressions, $expPrefix);
            }
            elseif ($data instanceof \SimpleXMLElement) {
                $data = (array) $data;
            }
            else {
                $result = [];
                foreach ($data as $name => $value) {
                    $result[$name] = $value;
                }
                $data = $result;
            }
            if ($data === []) {
                return new \stdClass();
            }
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $data[$key] = static::processData($value, $expressions, $expPrefix);
                }
            }
        }
        return $data;
    }
}