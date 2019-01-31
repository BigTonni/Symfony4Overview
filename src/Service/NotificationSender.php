<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class NotificationSender
{
    private $em;
    private $templating;
    private $mailer;

    /**
     * @param EntityManagerInterface $em
     * @param EngineInterface $templating
     * @param \Swift_Mailer $mailer
     */
    public function __construct(EntityManagerInterface $em, EngineInterface $templating, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    /**
     * @param Article $article
     * @param User $currentUser
     */
    public function sendNotification(Article $article, User $currentUser): void
    {
        $users = $this->em->getRepository(Subscription::class)->findBy(
            [
                'isSend' => false,
            ]
        );
        foreach ($users as $user) {
            if ($currentUser->getId() !== $user->getId()) {
                $this->sendMail($user, $article);
            }
        }
    }

    /**
     * @param User $user
     * @param Article $article
     */
    public function sendMail(User $user, Article $article): void
    {
        $message = (new \Swift_Message('New notification'))
            ->setFrom('admin@example.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'emails/notification.html.twig',
                    [
                        'username' => $user->getUserName(),
                        'articleTitle' => $article->getTitle(),
                    ]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
}
