<?php

namespace App\Traits;

trait HasDatatableParams
{
    protected $page;
    protected $limit;
    protected $cari;

    public function getPage()
    {
        return $this->page;
    }

    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function getCari()
    {
        return $this->cari;
    }

    public function setCari($cari)
    {
        $this->cari = $cari;
        return $this;
    }
}
