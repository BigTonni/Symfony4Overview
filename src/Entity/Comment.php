<?php

namespace App\Entity;

//use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class Comment
{
    /**
     * @var int
     */
    private $id;
    
    /**
     * @var Article
     * @Assert\NotBlank()
     */
    private $article;
    
    /**
     * @var string
     * @Assert\NotNull
     * @Assert\Length(
     *     min=5,
     *     max=100
     * )
     */
    private $content;
    
    /**
     * @var \DateTime
     * @Assert\DateTime
     */
    private $publishedAt;
    
    /**
     * @var User
     * @Assert\NotBlank()
     */
    private $author;
    
    public function __construct()
    {
        $this->publishedAt = new \DateTime();
    }
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getContent(): ?string
    {
        return $this->content;
    }
    
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
    
    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }
    
    public function setPublishedAt(\DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }
    
    public function getAuthor(): User
    {
        return $this->author;
    }
    
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }
    
    public function getArticle(): Article
    {
        return $this->article;
    }
    
    public function setArticle(Article $article): void
    {
        $this->article = $article;
    }
}
