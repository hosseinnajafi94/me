<?php
namespace me\widgets;
use Me;
use me\helpers\ArrayHelper;
class DetailView extends Widget {
    /**
     * @var string Table Html
     */
    public $table;
    /**
     * @var \me\components\Model
     */
    public $model;
    /**
     * @var Column[]
     */
    public $columns = [];
    public function init() {
        parent::init();
        foreach ($this->columns as $key => $id) {
            if (is_array($id)) {
                $config              = ArrayHelper::Extend([
                            'class' => 'me\widgets\Column',
                            'temp'  => $this->model,
                            'data'  => $this->model
                                ], $id);
                $this->columns[$key] = Me::createObject($config);
            }
            else if (is_string($id)) {
                $this->columns[$key] = Me::createObject([
                            'class'     => 'me\widgets\Column',
                            'attribute' => $id,
                            'temp'      => $this->model,
                            'data'      => $this->model
                ]);
            }
        }
        $this->table = $this->renderTable();
    }
    public function __toString() {
        return $this->table;
    }
    protected function renderTable() {
        $table = "\n";
        $table .= '<table class="table table-bordered table-striped">' . "\n";
        $table .= '  <tbody>' . "\n";
        foreach ($this->columns as $column) {
            $table .= '    <tr>';
            $table .= '<th>' . $column->title() . '</th>';
            $table .= '<td>' . $column->value($this->model) . '</td>';
            $table .= '</tr>' . "\n";
        }
        $table .= '  </tbody>' . "\n";
        $table .= '</table>' . "\n\n";
        return $table;
    }
}