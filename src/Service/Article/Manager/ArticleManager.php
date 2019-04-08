<?php

namespace App\Service\Article\Manager;

use App\Entity\Article;
use App\Entity\Subscription;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleManager
{
    private $tokenStorage;

    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        Uploader $uploader,
        EntityManagerInterface $em
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->uploader = $uploader;
        $this->em = $em;
    }

    public function create(Article $article): void
    {
        $article->setAuthor($this->tokenStorage->getToken()->getUser());
        if (null !== $article->getImage()) {
            if ($this->uploader->hasNewImage($article->getImage())) {
                $this->uploadImage($article);
            }
        }
        $this->em->persist($article);
        $this->em->flush();
    }

    public function edit(Article $article): void
    {
        $image = $article->getImage();

        if (null !== $image) {
            if ($this->uploader->hasNewImage($image)) {
                if ($this->uploader->hasActiveImage($image)) {
                    $this->uploader->removeImage($image->getAlt());
                }
                $this->uploadImage($article);
            } else {
                if ($this->uploader->hasActiveImage($image) && $this->uploader->isDeleteImageChecked($image)) {
                    $this->uploader->removeImage($image->getAlt());
                    $this->em->remove($image);
                    $article->setImage(null);
                }
            }
        }

        $this->em->flush();
    }

    public function remove(Article $article): void
    {
        //Remove article image
        if (null !== $image = $article->getImage()) {
            if ($this->uploader->hasNewImage($image)) {
                if ($this->uploader->hasActiveImage($image)) {
                    $this->uploader->removeImage($image->getAlt());
                }
            }
        }

        $this->em->remove($article);
        $this->em->flush();
    }

    /**
     * @throws \Exception
     * @return Article[]|object[]
     */
    public function getNewArticlesInSubscribedCategoriesToday()
    {
        $currUser = $this->tokenStorage->getToken()->getUser();
        $batchSize = 20;
        $i = 0;
        $currDate = new \DateTime();

        $iterableSubscribedCategories = $this->em->getRepository(Subscription::class)
            ->getTodaySubscriptionsByUserQuery($currDate, $currUser)
            ->iterate()
        ;

        $subscribedCategories = [];
        foreach ($iterableSubscribedCategories as $subscriber) {
            $arrPersistentCollection = $subscriber[0]->getCategories()->getValues();
            if (!empty($arrPersistentCollection)) {
                $subscribedCategories[$arrPersistentCollection[0]->getId()] = $arrPersistentCollection[0]->getTitle();
            }
            if (($i % $batchSize) === 0) {
                $this->em->flush(); // Executes all updates.
                $this->em->clear(); // Detaches all objects from Doctrine!
            }
            ++$i;
        }

        $articles = [];

        if (\count($subscribedCategories) === 0) {
            return $articles;
        }

        //Get articles
        foreach ($subscribedCategories as $catId => $catTitle) {
            $articles[$catTitle] = $this->em->getRepository(Article::class)->getTodayArticlesInSubscribedCategories(
                $currDate,
                $currUser->getId(),
                $catId
            );
        }

        return $articles;
    }

    private function uploadImage(Article $article): void
    {
        $alt = $this->uploader->generateAlt($article->getImage()->getFile());
        $article->getImage()->setAlt($alt);
        $this->uploader->uploadImage($article->getImage()->getFile(), $alt);
    }
}
