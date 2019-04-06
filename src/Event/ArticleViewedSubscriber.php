<?php

namespace App\Event;

//use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticleViewedSubscriber extends AbstractController implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
//            ArticleViewedEvent::NAME => 'onArticleViewed',
        ];
    }

//    public function onArticleViewed(ArticleViewedEvent $articleViewedEvent)
//    {
//        $article = $articleViewedEvent->getArticle();
//        $em = $this->getDoctrine()->getManager();
//        $articleInNotification = $em->getRepository(Notification::class)->findBy(
//            [
//                'article' => $articleViewedEvent->getArticle(),
//                'user' => $this->getUser(),
//            ]
//        );
//        if ($articleInNotification) {
//            $em->getRepository(Notification::class)->updateReadStatus($article, $this->getUser(), true);
//        }
//    }
}
