<?php

//namespace App\Entity;
//
//use Doctrine\ORM\Mapping as ORM;
//
///**
// * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
// */
//class Notification
//{
//    /**
//     * @ORM\Id()
//     * @ORM\GeneratedValue()
//     * @ORM\Column(type="integer")
//     */
//    private $id;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="App\Entity\User")
//     * @ORM\JoinColumn(nullable=false)
//     */
//    private $user;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="App\Entity\Article")
//     * @ORM\JoinColumn(nullable=false)
//     */
//    private $article;
//
//    /**
//     * @ORM\Column(type="boolean")
//     */
//    private $isRead = false;
//
//    /**
//     * @return int|null
//     */
//    public function getId(): ?int
//    {
//        return $this->id;
//    }
//
//    /**
//     * @return User|null
//     */
//    public function getUser(): ?User
//    {
//        return $this->user;
//    }
//
//    /**
//     * @param User|null $user
//     *
//     * @return Notification
//     */
//    public function setUser(?User $user): self
//    {
//        $this->user = $user;
//
//        return $this;
//    }
//
//    /**
//     * @return Article|null
//     */
//    public function getArticle(): ?Article
//    {
//        return $this->article;
//    }
//
//    /**
//     * @param Article|null $article
//     *
//     * @return Notification
//     */
//    public function setArticle(?Article $article): self
//    {
//        $this->article = $article;
//
//        return $this;
//    }
//
//    /**
//     * @return bool
//     */
//    public function isRead(): bool
//    {
//        return $this->isRead;
//    }
//
//    /**
//     * @param bool $isRead
//     */
//    public function setIsRead(bool $isRead): void
//    {
//        $this->isRead = $isRead;
//    }
//}
