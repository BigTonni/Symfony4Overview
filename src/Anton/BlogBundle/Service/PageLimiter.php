<?php

namespace App\Anton\BlogBundle\Service;

class PageLimiter
{
    private $limit;

    /**
     * PageLimiter constructor.
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
