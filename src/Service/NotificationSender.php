<?php

namespace App\Service;

use App\Entity\Article;
//use App\Entity\Notification;
use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationSender
{
    private $em;
    private $templating;
    private $mailer;
    private $router;
    private $fromEmail;
    private $translator;

    /**
     * @param EntityManagerInterface $em
     * @param EngineInterface $templating
     * @param RouterInterface $router
     * @param string $fromEmail
     * @param TranslatorInterface $translator
     * @param \Swift_Mailer $mailer
     */
    public function __construct(
        EntityManagerInterface $em,
        EngineInterface $templating,
        RouterInterface $router,
        string $fromEmail,
        TranslatorInterface $translator,
        \Swift_Mailer $mailer
    ) {
        $this->em = $em;
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->fromEmail = $fromEmail;
        $this->translator = $translator;
    }

//    /**
//     * @param User $user
//     * @return array
//     */
//    public function localNotification(User $user): array
//    {
//        $notification = $this->em->getRepository(Notification::class)->findBy([
//            'user' => $user,
//            'isRead' => false,
//        ], [], 5);
//
//        return $notification;
//    }

    public function sendNotification(): void
    {
        $batchSize = 20;
        $i = 0;
        $currDate = new \DateTime();

        $iterableSubscribedCategories = $this->em->getRepository(Subscription::class)
            ->getTodaySubscriptionsQuery($currDate)
            ->iterate()
        ;

        $subscribedCategoriesGroupByUser = [];
        foreach ($iterableSubscribedCategories as $subscriber) {
            $arrPersistentCollection = $subscriber[0]->getCategories()->getValues();
            if (!empty($arrPersistentCollection)) {
                $subscribedCategoriesGroupByUser[$subscriber[0]->getUser()->getId()] = $arrPersistentCollection[0]->getId();
            }
            if (($i % $batchSize) === 0) {
                $this->em->flush(); // Executes all updates.
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
            ++$i;
        }

        if (\count($subscribedCategoriesGroupByUser) === 0) {
            return;
        }

        //Get articles
        $articles = [];
        $i = 0;

        foreach ($subscribedCategoriesGroupByUser as $userId => $catId) {
            $articles[] = $this->em->getRepository(Article::class)->getTodayArticlesInSubscribedCategories(
                $currDate,
                $userId,
                $catId
            );

            if (!empty($articles)) {
                $user = $this->getDoctrine()
                    ->getRepository('User')
                    ->findBy(['id' => $userId]);

                $userEmail = $user->getEmail();
                $userName = $user->getUsername();

                foreach ($articles as $key => $article) {
                    if ($article->getSlug() !== null) {
                        $url = $this->router->generate('article_show', ['slug' => $article->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
                    } else {
                        $url = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
                    }

                    $this->sendMail($userEmail, $userName, $article, $url);
                }

                if (($i % $batchSize) === 0) {
                    $this->em->flush(); // Executes all updates.
                    $this->em->clear(); // Detaches all objects from Doctrine!
                }
                ++$i;
            }
        }
    }

    /**
     * @param string $user_email
     * @param string $user_name
     * @param Article $article
     * @param string $url
     */
    public function sendMail($user_email, $user_name, Article $article, $url): void
    {
        $message = (new \Swift_Message($this->translator->trans('send.notification_new')))
            ->setFrom($this->fromEmail)
            ->setTo($user_email)
            ->setBody(
                $this->templating->render(
                    'emails/notification.html.twig',
                    [
                        'username' => $user_name,
                        'article' => $article,
                        'articleUrl' => $url,
                    ]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
}
