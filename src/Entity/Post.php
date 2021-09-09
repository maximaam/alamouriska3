<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\PostTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\PostRepository;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @UniqueEntity(fields={"title"}, message="post_exists")
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{
    use PostTrait, TimestampableEntity;

    public const PAGINATOR_MAX = 20;
    public const SLUG_LIMIT = 128;
    public const TYPE_WORD = 1;
    public const TYPE_EXPRESSION = 2;
    public const TYPE_PROVERB = 3;
    public const TYPE_JOKE = 4;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @Assert\Choice(callback="getTypes")
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank
     */
    private ?int $type;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isQuestion = false;

    /**
     * @ORM\OneToOne(targetEntity=Image::class, cascade={"persist", "remove"})
     */
    private ?Image $image = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isQuestion(): ?bool
    {
        return $this->isQuestion;
    }

    public function setIsQuestion(bool $isQuestion): self
    {
        $this->isQuestion = $isQuestion;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
