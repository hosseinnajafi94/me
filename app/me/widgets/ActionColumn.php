<?php
namespace me\widgets;
use me\helpers\Html;
use me\components\Model;
class ActionColumn extends Column {
    public $template = '{detail} {update} {delete}';
    public $buttons  = [];
    public function init() {
        parent::init();
        if (empty($this->buttons)) {
            $this->buttons = [
                'detail' => function(Model $model) {
                    /* @var $model Model */
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['detail', 'id' => $model->id]);
                },
                'update' => function(Model $model) {
                    /* @var $model Model */
                    return Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['update', 'id' => $model->id]);
                },
                'delete' => function(Model $model) {
                    /* @var $model Model */
                    return Html::a('<i class="glyphicon glyphicon-trash"></i>', ['delete', 'id' => $model->id]);
                },
            ];
        }
    }
    public function title() {
        return '';
    }
    public function load(Model $model) {
        $str = '';
        $matches = [];
        preg_match_all('/[*{]+[a-zA-Z0-9.]+[*}]/', $this->template, $matches);
        $matches = str_replace(['{', '}'], '', $matches[0]);
        foreach ($matches as $match) {
            $str .= call_user_func_array($this->buttons[$match], [$model]);
        }
        return $str;
    }
}