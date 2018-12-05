<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
//use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class Article
{
    /**
     * @var int
     * @Assert\NotNull
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $id;
    
    /**
     * @var string
     * @Assert\NotBlank
     */
    private $title;
    
    /**
     * @var string
     * @Assert\NotNull
     * @Assert\Type("string")
     */
    private $slug;
    
    /**
     * @var string
     * @Assert\NotBlank(message="article.blank_body")
     * @Assert\Length(min=10, minMessage="article.too_short_content")
     */
    private $body;
    
    /**
     * @var \DateTime
     * @Assert\DateTime
     * @var string A "d-m-Y" formatted value
     */
    private $publishedAt;
    
    /**
     * @var User
     */
    private $author;
    
    /**
     * @var Comment[]|ArrayCollection
     */
    private $comments;
    
    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->comments = new ArrayCollection();
    }
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
    
    public function getSlug(): string
    {
        return $this->slug;
    }
    
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }
    
    public function getBody(): string
    {
        return $this->body;
    }
    
    public function setBody(string $body): void
    {
        $this->body = $body;
    }
    
    public function getPublishedAt(): \DateTime
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
    
    public function getComments(): Collection
    {
        return $this->comments;
    }
    
    public function addComment(Comment $comment): void
    {
        $comment->setPost($this);
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
    }
    
    public function removeComment(Comment $comment): void
    {
        $comment->setPost(null);
        $this->comments->removeElement($comment);
    }    
}
