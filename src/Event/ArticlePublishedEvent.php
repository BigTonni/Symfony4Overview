<?php

namespace App\Event;

use App\Entity\Article;
use Symfony\Component\EventDispatcher\Event;

class ArticlePublishedEvent extends Event
{
    public const NAME = 'article.published';

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
