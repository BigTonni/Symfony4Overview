<?php

namespace App\Event;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticleViewedSubscriber extends AbstractController implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ArticleViewedEvent::NAME => 'onArticleViewed',
        ];
    }

    public function onArticleViewed(ArticleViewedEvent $articleViewedEvent)
    {
        //code...
    }
}
