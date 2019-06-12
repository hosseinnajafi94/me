<?php
namespace me\db;
use me\components\Component;
use me\helpers\StringHelper;
class ColumnSchema extends Component {
    public $name;
    public $dbType;
    public $allowNull;
    public $isPrimaryKey;
    public $defaultValue;
    public $autoIncrement = false;
    public $comment;
    public $type;
    public $unsigned;
    public $enumValues;
    public $size;
    public $precision;
    public $scale;
    public $phpType;
    /**
     * @param mixed $value
     * @return null|int|string|array|bool
     */
    public function typecast($value) {
        if ($value === '' && !in_array($this->type, [Schema::TYPE_TEXT, Schema::TYPE_STRING, Schema::TYPE_BINARY, Schema::TYPE_CHAR], true)) {
            return null;
        }
        if ($value === null || gettype($value) === $this->phpType) {
            return $value;
        }
        switch ($this->phpType) {
            case 'resource':
            case 'string':
                if (is_resource($value)) {
                    return $value;
                }
                if (is_float($value)) {
                    // ensure type cast always has . as decimal separator in all locales
                    return StringHelper::floatToString($value);
                }
                return (string) $value;
            case 'integer':
                return (int) $value;
            case 'boolean':
                // treating a 0 bit value as false too
                return (bool) $value && $value !== "\0";
            case 'double':
                return (float) $value;
        }
        return $value;
    }
}