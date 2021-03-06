<?php

namespace App\Event;

use App\Entity\Notification;
use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticlePublishedSubscriber extends AbstractController implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ArticlePublishedEvent::NAME => 'onArticlePublished',
        ];
    }

    public function onArticlePublished(ArticlePublishedEvent $articlePublishedEvent)
    {
        $article = $articlePublishedEvent->getArticle();
        $category = $article->getCategory();

        $em = $this->getDoctrine()->getManager();
        $subscribers = $em->getRepository(Subscription::class)->findBy(
            [
                'category' => $category,
            ]
        );

        if ($subscribers) {
            foreach ($subscribers as $subscriber) {
                $notification = new Notification();
                $notification->setUser($subscriber->getUser());
                $notification->setArticle($article);

                $em->persist($notification);
            }

            $em->flush();
        }
    }
}
