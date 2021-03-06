<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\Table(name="articles")
 * @Serializer\ExclusionPolicy("all")
 */
class Article
{
    use TimestampableEntity;

    public const NUM_ITEMS = 5;

    public const STATUS_DRAFT = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_PUBLISH = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Serializer\Expose()
     */
    private $title;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Serializer\Expose()
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(min=10)
     * @Serializer\Expose()
     */
    private $body;

    /**
     * @ORM\Column(type="smallint")
     * @Serializer\Expose()
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @var User
     *
     * @Gedmo\Blameable(on="change", field={"title", "body"})
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Comment",
     *     mappedBy="article",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="articles")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Like", mappedBy="article", orphanRemoval=true, cascade={"persist"})
     */
    private $likes;

    /**
     * @var Image
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid()
     */
    private $image;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Article
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function isAuthor(User $author = null)
    {
        return $author && $author->getEmail() === $this->getAuthorEmail();
    }

    /**
     * @return Article[]|Collection
     */
    public function getComments(): ?Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @param Tag $tag
     * @return Article
     */
    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function getTags(): ?Collection
    {
        return $this->tags;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    /**
     * @param Like $like
     * @return Article
     */
    public function addLike(?Like $like): self
    {
        $like->setArticle($this);
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }

        return $this;
    }

    /**
     * @param Like $like
     * @return Article
     */
    public function removeLike(?Like $like): self
    {
        $like->setArticle(null);
        $this->likes->removeElement($like);

        return $this;
    }

    public function setImage(Image $image = null): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    private function getAuthorEmail()
    {
        return $this->author->getEmail();
    }
}
