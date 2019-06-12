<?php
namespace me\helpers;
use Me;
use me\components\Model;
class Html extends Helper {
    public static function optionToAttr(array $options = []): string {
        $attrs = '';
        foreach ($options as $key => $value) {
            if (!is_null($value)) {
                if (is_bool($value)) {
                    if ($value === true) {
                        $attrs .= ' ' . $key . '="' . ($key === 'checked' || $key === 'selected' ? $key : $value) . '"';
                    }
                }
                else {
                    $attrs .= ' ' . $key . '="' . $value . '"';
                }
            }
        }
        return $attrs;
    }
    //
    public static function input(string $type, array $options = []): string {
        return '<input' . self::optionToAttr(array_merge(['type' => $type], $options)) . '/>';
    }
    public static function submitButton(string $text, array $options = []) {
        return static::input('submit', ArrayHelper::Extend(['value' => $text], $options)) . "\n";
    }
    //
    public static function textInput(array $options = []): string {
        return self::input('text', $options);
    }
    public static function passwordInput(array $options = []): string {
        return self::input('password', $options);
    }
    public static function fileInput(array $options = []): string {
        return self::input('file', $options);
    }
    public static function numberInput(array $options = []): string {
        return self::input('file', $options);
    }
    public static function colorInput(array $options = []): string {
        return self::input('color', $options);
    }
    public static function hiddenInput(array $options = []): string {
        return static::input('hidden', $options);
    }
    public static function radio(array $options = []): string {
        return static::booleanInput('radio', $options);
    }
    public static function checkbox(array $options = []): string {
        return static::booleanInput('checkbox', $options);
    }
    public static function dropDownList($name, $selection = null, $items = [], $options = []) {
        if (!empty($options['multiple'])) {
            return static::listBox($name, $selection, $items, $options);
        }
        $options['name'] = $name;
        unset($options['unselect']);
        $selectOptions   = static::renderSelectOptions($selection, $items, $options);
        return '<select' . static::optionToAttr($options) . '>' . $selectOptions . '</select>';
    }
    public static function listBox($name, $selection = null, $items = [], $options = []) {
        if (!array_key_exists('size', $options)) {
            $options['size'] = 4;
        }
        if (!empty($options['multiple']) && !empty($name) && substr_compare($name, '[]', -2, 2)) {
            $name .= '[]';
        }
        $options['name'] = $name;
        if (isset($options['unselect'])) {
            // add a hidden field so that if the list box has no option being selected, it still submits a value
            if (!empty($name) && substr_compare($name, '[]', -2, 2) === 0) {
                $name = substr($name, 0, -2);
            }
            $hiddenOptions = [];
            // make sure disabled input is not sending any value
            if (!empty($options['disabled'])) {
                $hiddenOptions['disabled'] = $options['disabled'];
            }
            $hidden = static::hiddenInput(ArrayHelper::Extend(['name' => $name, 'value' => $options['unselect']], $hiddenOptions));
            unset($options['unselect']);
        }
        else {
            $hidden = '';
        }
        $selectOptions = static::renderSelectOptions($selection, $items, $options);
        return $hidden . '<select' . static::optionToAttr($options) . '>' . $selectOptions . '</select>';
    }
    public static function checkboxList($name, $selection = null, $items = [], $options = []) {
        if (substr($name, -2) !== '[]') {
            $name .= '[]';
        }
        if (ArrayHelper::isTraversable($selection)) {
            $selection = array_map('strval', (array) $selection);
        }
        $itemOptions = ArrayHelper::Remove($options, 'itemOptions', []);
        $lines       = [];
        foreach ($items as $value => $label) {
            $checked = $selection !== null && (!ArrayHelper::isTraversable($selection) && !strcmp($value, $selection) || ArrayHelper::isTraversable($selection) && ArrayHelper::isIn((string) $value, $selection));
            $lines[] = static::checkbox(ArrayHelper::Extend(['name' => $name, 'checked' => $checked, 'value' => $value, 'label' => $label], $itemOptions));
        }

        $hidden = '';
        if (isset($options['unselect'])) {
            // add a hidden field so that if the list box has no option being selected, it still submits a value
            $name2         = substr($name, -2) === '[]' ? substr($name, 0, -2) : $name;
            $hiddenOptions = [];
            // make sure disabled input is not sending any value
            if (!empty($options['disabled'])) {
                $hiddenOptions['disabled'] = $options['disabled'];
            }
            $hidden = static::hiddenInput(ArrayHelper::Extend(['name' => $name2, 'value' => $options['unselect']], $hiddenOptions));
            unset($options['unselect'], $options['disabled']);
        }
        return $hidden . '<div' . static::optionToAttr($options) . '>' . implode("", $lines) . '</div>';
    }
    public static function radioList($name, $selection = null, $items = [], $options = []) {
        if (ArrayHelper::isTraversable($selection)) {
            $selection = array_map('strval', (array) $selection);
        }
        $itemOptions = ArrayHelper::Remove($options, 'itemOptions', []);
        $hidden      = '';
        if (isset($options['unselect'])) {
            // add a hidden field so that if the list box has no option being selected, it still submits a value
            $hiddenOptions = [];
            // make sure disabled input is not sending any value
            if (!empty($options['disabled'])) {
                $hiddenOptions['disabled'] = $options['disabled'];
            }
            $hidden = static::hiddenInput(ArrayHelper::Extend($hiddenOptions, ['name' => $name, 'value' => $options['unselect']]));
            unset($options['unselect'], $options['disabled']);
        }
        $lines = [];
        foreach ($items as $value => $label) {
            $checked = $selection !== null && (!ArrayHelper::isTraversable($selection) && !strcmp($value, $selection) || ArrayHelper::isTraversable($selection) && ArrayHelper::isIn((string) $value, $selection));
            $lines[] = static::radio(array_merge(['name' => $name, 'checked' => $checked, 'value' => $value, 'label' => $label], $itemOptions));
        }
        return $hidden . '<div' . static::optionToAttr($options) . '>' . implode("", $lines) . '</div>';
    }
    public static function renderSelectOptions($selection, $items, &$tagOptions = []) {
        if (ArrayHelper::isTraversable($selection)) {
            $selection = array_map('strval', (array) $selection);
        }
        $lines = [];
        if (isset($tagOptions['prompt'])) {
            $promptOptions = ['value' => ''];
            if (is_string($tagOptions['prompt'])) {
                $promptText = $tagOptions['prompt'];
            }
            else {
                $promptText    = $tagOptions['prompt']['text'];
                $promptOptions = array_merge($promptOptions, $tagOptions['prompt']['options']);
            }
            $lines[] = '<option' . static::optionToAttr($promptOptions) . '>' . $promptText . '</option>';
        }
        $options = isset($tagOptions['options']) ? $tagOptions['options'] : [];
        $groups  = isset($tagOptions['groups']) ? $tagOptions['groups'] : [];
        unset($tagOptions['prompt'], $tagOptions['options'], $tagOptions['groups']);
        foreach ($items as $key => $value) {
            if (is_array($value)) {
                $groupAttrs = isset($groups[$key]) ? $groups[$key] : [];
                if (!isset($groupAttrs['label'])) {
                    $groupAttrs['label'] = $key;
                }
                $attrs   = ['options' => $options, 'groups' => $groups];
                $content = static::renderSelectOptions($selection, $value, $attrs);
                $lines[] = '<optgroup' . static::optionToAttr($groupAttrs) . '>' . $content . '</optgroup>';
            }
            else {
                $attrs          = isset($options[$key]) ? $options[$key] : [];
                $attrs['value'] = (string) $key;
                if (!array_key_exists('selected', $attrs)) {
                    $attrs['selected'] = $selection !== null && (!ArrayHelper::isTraversable($selection) && !strcmp($key, $selection) || ArrayHelper::isTraversable($selection) && ArrayHelper::isIn((string) $key, $selection));
                }
                $lines[] = '<option' . static::optionToAttr($attrs) . '>' . $value . '</option>';
            }
        }
        return implode("", $lines);
    }
    protected static function booleanInput($type, $options = []) {
        $name   = $options['name'];
        $value  = array_key_exists('value', $options) ? $options['value'] : '1';
        $hidden = '';
        if (isset($options['uncheck'])) {
            // add a hidden field so that if the checkbox is not selected, it still submits a value
            $hiddenOptions = [];
            if (isset($options['form'])) {
                $hiddenOptions['form'] = $options['form'];
            }
            // make sure disabled input is not sending any value
            if (!empty($options['disabled'])) {
                $hiddenOptions['disabled'] = $options['disabled'];
            }
            $hidden = static::hiddenInput(ArrayHelper::Extend($hiddenOptions, ['name' => $name, 'value' => $options['uncheck']]));
            unset($options['uncheck']);
        }
        if (isset($options['label'])) {
            $label        = $options['label'];
            $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
            unset($options['label'], $options['labelOptions']);
            $input        = static::input($type, ArrayHelper::Extend($options, ['name' => $name, 'value' => $value]));
            $content      = static::label($input . $label, $labelOptions);
            return $hidden . $content;
        }
        $input = static::input($type, ArrayHelper::Extend($options, ['name' => $name, 'value' => $value]));
        return $hidden . $input;
    }
    //
    public static function a(string $text, $url = null, array $options = []): string {
        return '<a' . self::optionToAttr(array_merge(['href' => (is_array($url) ? Url::to($url) : $url)], $options)) . '>' . $text . "</a>\n";
    }
    public static function script(string $text): string {
        return '<script type="text/javascript">' . $text . '</script>';
    }
    public static function scriptLink(string $src, array $options = ['type' => 'text/javascript']): string {
        return '<script' . self::optionToAttr($options) . ' src="' . $src . '"></script>';
    }
    public static function link(string $rel, array $options = []): string {
        return '<link rel="' . $rel . '"' . self::optionToAttr($options) . '/>';
    }
    public static function cssLink(string $href): string {
        return self::link('stylesheet', ['type' => 'text/css', 'href' => $href]);
    }
    public static function style(string $css): string {
        return "<style>$css</style>";
    }
    //
    public static function beginForm($action, $method, $options) {
        $options = ArrayHelper::Extend(['method' => $method, 'action' => $action ? Url::to($action) : ''], $options);
        return '<form' . static::optionToAttr($options) . '>';
    }
    public static function endForm() {
        return '</form>';
    }
    public static function getInputName(Model $model, string $attribute) {
        $formName = $model->formName();
        if ($formName === '') {
            return $attribute;
        }
        else {
            return $formName . "[$attribute]";
        }
    }
    public static function getInputId(Model $model, string $attribute) {
        $charset = Me::$app ? Me::$app->charset : 'UTF-8';
        $name    = mb_strtolower(static::getInputName($model, $attribute), $charset);
        return str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], $name);
    }
    public static function getAttributeValue($model, $attribute) {
        return $model->$attribute;
    }
    public static function label(string $text, array $options = []): string {
        return '<label' . self::optionToAttr($options) . '>' . $text . '</label>';
    }
    public static function activeLabel(Model $model, string $attribute, array $options = []): string {
        $for   = ArrayHelper::remove($options, 'for', static::getInputId($model, $attribute));
        $label = ArrayHelper::Remove($options, 'label', $model->attributeLabel($attribute));
        return static::label($label, ArrayHelper::Extend($options, ['for' => $for]));
    }
    public static function activeHint(Model $model, string $attribute, array $options = []) {
        $hint = isset($options['hint']) ? $options['hint'] : $model->attributeHint($attribute);
        return '<div' . static::optionToAttr($options) . '>' . $hint . '</div>';
    }
    public static function error(Model $model, string $attribute, array $options = []) {
        $error = $model->getFirstError($attribute);
        return '<div' . static::optionToAttr($options) . '>' . $error . '</div>';
    }
    public static function activeInput($type, Model $model, string $attribute, array $options = []): string {
        $id      = static::getInputId($model, $attribute);
        $name    = static::getInputName($model, $attribute);
        $value   = static::getAttributeValue($model, $attribute);
        $options = ArrayHelper::Extend(['id' => $id, 'name' => $name, 'value' => $value], $options);
        return self::input($type, $options);
    }
    public static function activeTextInput(Model $model, string $attribute, array $options = []): string {
        return self::activeInput('text', $model, $attribute, $options);
    }
    public static function activePasswordInput(Model $model, string $attribute, array $options = []): string {
        return self::activeInput('password', $model, $attribute, $options);
    }
    public static function activeFileInput(Model $model, string $attribute, array $options = []): string {
        return self::activeInput('file', $model, $attribute, $options);
    }
    public static function activeRadioInput(Model $model, string $attribute, array $options = []): string {
        return self::activeInput('radio', $model, $attribute, $options);
    }
    public static function activeCheckboxInput(Model $model, string $attribute, array $options = []): string {
        return self::activeInput('checkbox', $model, $attribute, $options);
    }
    public static function activeColorInput(Model $model, string $attribute, array $options = []): string {
        return self::activeInput('color', $model, $attribute, $options);
    }
    public static function activeDropDownList($model, $attribute, $items, $options = []) {
        if (empty($options['multiple'])) {
            return static::activeListInput('dropDownList', $model, $attribute, $items, $options);
        }
        return static::activeListInput('listBox', $model, $attribute, $items, $options);
    }
    public static function activeListBox($model, $attribute, $items, $options = []) {
        return static::activeListInput('listBox', $model, $attribute, $items, $options);
    }
    public static function activeCheckboxList($model, $attribute, $items, $options = []) {
        return static::activeListInput('checkboxList', $model, $attribute, $items, $options);
    }
    public static function activeRadioList($model, $attribute, $items, $options = []) {
        return static::activeListInput('radioList', $model, $attribute, $items, $options);
    }
    protected static function activeListInput($type, $model, $attribute, $items, $options = []) {
        $name                = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        $selection           = isset($options['value']) ? $options['value'] : static::getAttributeValue($model, $attribute);
        $options['id']       = isset($options['id']) ? $options['id'] : static::getInputId($model, $attribute);
        $options['unselect'] = isset($options['unselect']) ? $options['unselect'] : '';
        return static::$type($name, $selection, $items, $options);
    }
}