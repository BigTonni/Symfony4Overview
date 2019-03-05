<?php

namespace App\Service;

class Paginator
{
    private $limit;

    public function __construct($limit)
    {
        $this->limit = $limit;
    }

    public function getLimit(): int
    {
        return (int) $this->limit;
    }
}
