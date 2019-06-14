<?php
namespace me\widgets;
use me\helpers\Html;
use me\components\Model;
class ActionColumn extends Column {
    public $template = '{detail} {update} {delete}';
    public $buttons  = [];
    public function init() {
        parent::init();
        if (!isset($this->buttons['detail'])) {
            $this->buttons['detail'] = function(Model $model) {
                /* @var $model Model */
                return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['detail', 'id' => $model->id]);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function(Model $model) {
                /* @var $model Model */
                return Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['update', 'id' => $model->id]);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function(Model $model) {
                /* @var $model Model */
                return Html::a('<i class="glyphicon glyphicon-trash"></i>', ['delete', 'id' => $model->id]);
            };
        }
    }
    public function title() {
        return '';
    }
    public function value(Model $model) {
        $buttons = [];
        $matches = [];
        preg_match_all('/[*{]+[a-zA-Z0-9.]+[*}]/', $this->template, $matches);
        $matches = str_replace(['{', '}'], '', $matches[0]);
        foreach ($matches as $match) {
            $buttons[] = trim(call_user_func_array($this->buttons[$match], [$model]));
        }
        return implode(" ", $buttons);
    }
}