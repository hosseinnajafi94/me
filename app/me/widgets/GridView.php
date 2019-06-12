<?php
namespace me\widgets;
class GridView extends Widget {
    /**
     * @var \me\data\ActiveDataProvider
     */
    public $data;
    /**
     * @var array
     */
    public $columns = [];
    public function __toString() {
        return $this->render();
    }
    protected function render() {
        /* @var $model \me\components\Model[] */
        $modelClass = $this->data->query->modelClass;
        /* @var $m \me\components\Model */
        $m          = (new $modelClass);
        $models     = $this->data->models();
        $table      = '';
        $table      .= '<table class="table table-bordered table-striped">' . "\n";
        $table      .= '  <thead>' . "\n";
        $table      .= '    <tr>';
        foreach ($this->columns as $column) {
            $table .= '<th>' . $m->attributeLabel($column) . '</th>';
        }
        $table .= '</tr>' . "\n";
        $table .= '  </thead>' . "\n";
        $table .= '  <tbody>' . "\n";
        foreach ($models as $model) {
            $table      .= '    <tr>';
            foreach ($this->columns as $column) {
                $table .= '<td>' . $model->$column . '</td>';
            }
            $table      .= '</tr>' . "\n";
        }
        $table .= '  </tbody>' . "\n";
        $table .= '</table>' . "\n";
        return $table;
    }
}