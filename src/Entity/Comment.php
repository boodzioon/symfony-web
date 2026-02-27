<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: '`comments`')]
#[ORM\HasLifecycleCallbacks]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'NotBlank')]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTime $created_at = null;
    private ?\DateTime $created_at2;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: 'author_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, name: 'video_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Video $video = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        if (isset($this->created_at2)) {
            $this->created_at = $this->created_at2;
        } else {
            $this->created_at = new \DateTime;
        }

        return $this;
    }

    public function  setCreatedAtForFixtures(\DateTime $created_at): static
    {
        $this->created_at2 = $created_at;

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

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function setVideo(?Video $video): static
    {
        $this->video = $video;

        return $this;
    }
}
