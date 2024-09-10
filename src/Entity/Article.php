<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Gedmo\Slug(fields: ['title'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $body = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $likeCount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFilename = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class, fetch: 'EXTRA_LAZY')]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'articles', cascade: ['persist'])]
    private Collection $tags;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function isPublished(): bool
    {
        return null !== $this->getPublishedAt();
    }

    public function getLikeCount(): ?int
    {
        return $this->likeCount;
    }

    public function setLikeCount(?int $likeCount): self
    {
        $this->likeCount = $likeCount;

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): static
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getImagePath(): string
    {
        return 'images/' . $this->getImageFilename();
    }

    public function getAuthorAvatarPath(): string
    {
        return sprintf(
            'https://robohash.org/%s.png?set=set4',
            str_replace(' ', '_', $this->getAuthor())
        );
    }

    public function like(): static
    {
        $this->likeCount++;
        return $this;
    }

    public function dislike(): static
    {
        $this->likeCount--;
        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getNonDeletedComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    #[Assert\Callback()]
    public function validate(ExecutionContextInterface $context, $payload): void
    {
        if (mb_stripos($this->getTitle(), 'собак') !== false) {
            $context->buildViolation('Про собак писать запрещено')
                ->atPath('title')
                ->addViolation()
            ;
        }
    }
}
