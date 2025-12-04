<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DataTable extends Component
{
    public $headers;
    public $rows;
    public $actions;
    public $route;
    public $searchKey;
    public $entriesKey;
    public $pageKey;

    public function __construct(
        $headers, 
        $rows, 
        $actions = [], 
        $route = null,
        $searchKey = 'search',
        $entriesKey = 'entries',
        $pageKey = 'page'
    ) {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->actions = $actions;
        $this->route = $route ?? url()->current();
        $this->searchKey = $searchKey;
        $this->entriesKey = $entriesKey;
        $this->pageKey = $pageKey;
    }

    public function render()
    {
        return view('components.data-table');
    }
}

