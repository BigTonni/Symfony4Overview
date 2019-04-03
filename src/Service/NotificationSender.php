<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
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
     * @param RouterInterface $router
     * @param \Swift_Mailer $mailer
     */
    public function __construct(EntityManagerInterface $em, EngineInterface $templating, RouterInterface $router, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->router = $router;
    }

    /**
     * @param User $user
     * @return array
     */
    public function localNotification(User $user): array
    {
        $notification = $this->em->getRepository(Notification::class)->findBy([
            'user' => $user,
            'isRead' => false,
        ]);

        return $notification;
    }

    public function sendNotification(): void
    {
        $notifications = $this->em->getRepository(Notification::class)->selectUsersByReadStatus(false);

        if (!empty($notifications)) {
            $users = [];
            foreach ($notifications as $notification) {
                $subscriber = $notification->getUser();
                $subscriber_email = $subscriber->getEmail();
                $users[$subscriber_email]['articles'][] = $notification->getArticle();
                $users[$subscriber_email]['username'] = $subscriber->getUsername();
                //For unsubscribe-link
                $users[$subscriber_email]['sub_id'] = $notification->getId();
            }

            foreach ($users as $user_email => $user) {
                foreach ($user['articles'] as $key => $article) {
                    if ($article->getSlug() !== null) {
                        $url = $this->router->generate('article_show', ['slug' => $article->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
                    } else {
                        $url = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
                    }
                    $user['articles'][$key]['url'] = $url;
                }

                $this->sendMail($user_email, $user['username'], $user['articles']);
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
