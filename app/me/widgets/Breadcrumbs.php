<?php
namespace me\widgets;
use me\helpers\Url;
class Breadcrumbs extends Widget {
    public $items = [];
    public function __toString() {
        return $this->render();
    }
    public function render() {
        $li = ['<li><a href="' . Url::to(['dashboard/default/index']) . '">داشبورد</a></li>'];
        if (is_array($this->items)) {
            foreach ($this->items as $breadcrumb) {
                $label = '';
                $url   = '';
                $class = '';
                if (is_string($breadcrumb)) {
                    $label = $breadcrumb;
                    $class = 'active';
                }
                elseif (is_array($breadcrumb)) {
                    $label = isset($breadcrumb['label']) ? $breadcrumb['label'] : '';
                    $url   = isset($breadcrumb['url']) ? Url::to($breadcrumb['url']) : '';
                    if (!$url) {
                        $class = 'active';
                    }
                }
                if ($label) {
                    $li[] = '<li class="' . $class . '">' . ($url ? '<a href="' . $url . '">' : '') . $label . ($url ? '</a>' : '') . '</li>';
                }
            }
        }
        return '<ul class="breadcrumb">' . implode('', $li) . '</ul>';
    }
}