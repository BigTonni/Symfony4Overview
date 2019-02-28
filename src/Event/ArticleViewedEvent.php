<?php

namespace App\Event;

use App\Entity\Article;
use Symfony\Component\EventDispatcher\Event;

class ArticleViewedEvent extends Event
{
    public const NAME = 'article.viewed';

    private $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function getArticle(): Article
    {
        return $this->article;
    }
}
