<?php
namespace me\data;
use me\components\Component;
class ActiveDataProvider extends Component {
    /**
     * @var \me\db\ActiveQuery
     */
    public $query;
    public $sort       = [];
    public $pagination = ['size' => 10];
    public $totalCount = 0;
    public $totalPage  = 0;
    public $page       = 1;
    public $order;
    public $limit      = 0;
    public $offset     = 0;
    public function models() {
        $this->totalCount = $this->query->count('*');
        $this->page       = intval(get('page', 1)) - 1;
        $this->order      = get('order');
        $size             = intval($this->pagination['size']);
        $this->totalPage  = intval(ceil($this->totalCount / $size));
        if (!empty($this->order)) {
            $this->query->order([$this->order => SORT_DESC]);
        }
        else if (!empty($this->sort)) {
            $this->query->order($this->sort);
        }
        $this->offset = $size * $this->page;
        $this->limit  = $size;
        $this->query->limit($this->limit);
        $this->query->offset($this->offset);
        // var_dump($this->query->createCommand()->sql);
        return $this->query->all();
    }
}