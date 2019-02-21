<?php

namespace App\Service\Article\Manager;

use App\Entity\Article;
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

    public function edit(Article $article)
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
        $this->em->remove($article);
        $this->em->flush();
    }

    private function uploadImage(Article $article): void
    {
        $alt = $this->uploader->generateAlt($article->getImage()->getFile());
        $article->getImage()->setAlt($alt);
        $this->uploader->uploadImage($article->getImage()->getFile(), $alt);
    }
}
