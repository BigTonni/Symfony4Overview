<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class NotificationSender
{
    private $em;
    private $templating;
    private $mailer;
    private $router;

    /**
     * @param EntityManagerInterface $em
     * @param EngineInterface $templating
     * @param \Swift_Mailer $mailer
     * @param RouterInterface $router
     */
    public function __construct(EntityManagerInterface $em, EngineInterface $templating, RouterInterface $router, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function sendNotification(): void
    {
        $subscriptions = $this->em->getRepository(Subscription::class)->findBy(
            ['isSend' => false]
        );

        if (!empty($subscriptions)) {
            $users = [];
            foreach ($subscriptions as $key => $subscription) {
                $articles = $this->em->getRepository(Article::class)
                    ->findTodayArticlesByCategoryId($subscription->getCategory()->getId());

                if (!empty($articles)) {
                    $subscriber_email = $subscription->getUser()->getEmail();
                    $users[$subscriber_email]['articles'][] = $articles;
                    $users[$subscriber_email]['username'] = $subscription->getUser()->getUsername();
                    //For unsubscribe-link
                    $users[$subscriber_email]['sub_id'] = $subscription->getId();
                }
            }

            foreach ($users as $user_email => $user) {
                $articles = [];
                foreach ($user['articles'][0] as $key => $article) {
                    $articles[] = [
                        'title' => $article->getTitle(),
                        'slug' => $this->router->generate('article_show', ['slug' => $article->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    ];
                }

                $this->sendMail($user_email, $user['username'], $articles);
            }
        }
    }

    /**
     * @param string $user_email
     * @param string $user_name
     * @param array $articles
     */
    public function sendMail($user_email, $user_name, $articles): void
    {
        $message = (new \Swift_Message('New notification'))
            ->setFrom('admin@example.com')
            ->setTo($user_email)
            ->setBody(
                $this->templating->render(
                    'emails/notification.html.twig',
                    [
                        'username' => $user_name,
                        'articles' => $articles,
                    ]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
}
