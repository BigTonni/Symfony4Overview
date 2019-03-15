<?php

namespace App\Anton\BlogBundle\Service;

class Paginator
{
    private $limit;

    /**
     * Paginator constructor.
     * @param int $limit
     */
    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    public function getLimit()
    {
        return $this->limit;
    }
}
