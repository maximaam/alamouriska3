<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\PostTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\PostRepository;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @UniqueEntity(fields={"title"}, message="post_exists")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    use PostTrait;

    const PAGINATOR_MAX = 20;
    const SLUG_LIMIT = 128;

    const TYPE_WORD = 1;
    const TYPE_EXPRESSION = 2;
    const TYPE_PROVERB = 3;
    const TYPE_JOKE = 4;

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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private ?string $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isQuestion = false;

    /**
     * @ORM\OneToOne(targetEntity=Image::class, cascade={"persist", "remove"})
     */
    private ?Image $image;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return $this
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isQuestion(): ?bool
    {
        return $this->isQuestion;
    }

    /**
     * @param bool $isQuestion
     * @return $this
     */
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

}
