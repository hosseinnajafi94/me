<?php
namespace me\widgets;
use Me;
use me\helpers\ArrayHelper;
class GridView extends Widget {
    /**
     * @var string Table Html
     */
    public $table;
    /**
     * @var string Pagination Html
     */
    public $pagination;
    /**
     * @var string
     */
    public $emptyTable      = 'empty';
    /**
     * @var string
     */
    public $emptyPagination = 'empty';
    /**
     * @var \me\components\Model[]
     */
    public $models          = [];
    /**
     * @var \me\data\ActiveDataProvider
     */
    public $data;
    /**
     * @var Column[]
     */
    public $columns         = [];
    /**
     * @var \me\components\Model
     */
    private $temp;
    public function init() {
        parent::init();
        $this->temp   = Me::createObject(['class' => $this->data->query->modelClass]);
        $this->models = $this->data->models();
        foreach ($this->columns as $key => $id) {
            if (is_array($id)) {
                $config              = ArrayHelper::Extend(['class' => 'me\widgets\Column', 'temp' => $this->temp, 'data' => $this->data], $id);
                $this->columns[$key] = Me::createObject($config);
            }
            else if (is_string($id)) {
                $this->columns[$key] = Me::createObject(['class' => 'me\widgets\Column', 'attribute' => $id, 'temp' => $this->temp, 'data' => $this->data]);
            }
        }
        $this->table      = $this->renderTable();
        $this->pagination = $this->renderPagination();
    }
    public function __toString() {
        return $this->table . "<br/>". $this->pagination;
    }
    protected function renderTable() {
        $table = "\n";
        $table .= '<table class="table table-bordered table-striped">' . "\n";
        $table .= '  <thead>' . "\n";
        $table .= '    <tr>';
        foreach ($this->columns as $column) {
            $table .= '<th>' . $column->title() . '</th>';
        }
        $table .= '</tr>' . "\n";
        $table .= '  </thead>' . "\n";
        $table .= '  <tbody>' . "\n";
        if ($this->data->totalCount === 0) {
            $table .= '    <tr><td colspan="' . count($this->columns) . '">' . $this->emptyTable . '</td></tr>' . "\n";
        }
        else {
            foreach ($this->models as $model) {
                $table .= '    <tr>';
                foreach ($this->columns as $column) {
                    $table .= '<td>' . $column->value($model) . '</td>';
                }
                $table .= '</tr>' . "\n";
            }
        }
        $table .= '  </tbody>' . "\n";
        $table .= '</table>' . "\n\n";
        return $table;
    }
    protected function renderPagination() {
        if ($this->data->totalCount === 0) {
            return $this->emptyPagination . "\n";
        }
        $page    = $this->data->page + 1;
        $isFirst = $page === 1;
        $isLast  = $page === $this->data->totalPage;
        $start   = $page > 3 ? $page - 3 : 1;
        $end     = $page + 3 > $this->data->totalPage ? $this->data->totalPage : $page + 3;
        if ($end < $this->data->totalPage && $page < 4) {
            $end = $end + 3 - ($page - 1);
            if ($end > $this->data->totalPage) {
                $end = $this->data->totalPage;
            }
        }
        if ($page > $this->data->totalPage - 3) {
            $start = $start - (3 - ($this->data->totalPage - $page));
            if ($start < 1) {
                $start = 1;
            }
        }
        $pagination = "\n";
        $pagination .= '<ul class="pagination pagination-sm">';
        $pagination .= '<li' . ($isFirst ? ' class="disabled"' : '') . '><a' . ($isFirst ? '' : ' href="?page=' . ($page - 1) . '"') . '>&laquo;</a></li>';
        for ($index = $start; $index <= $end; $index++) {
            $pagination .= '<li class="' . ($index === $page ? 'active' : '') . '"><a href="?page=' . $index . '">' . $index . '</a></li>';
        }
        $pagination .= '<li' . ($isLast ? ' class="disabled"' : '') . '><a' . ($isLast ? '' : ' href="?page=' . ($page + 1) . '"') . '>&raquo;</a></li>';
        $pagination .= '</ul><div class="clearfix"></div>';
        $pagination .= "\n\n";
        return $pagination;
    }
}