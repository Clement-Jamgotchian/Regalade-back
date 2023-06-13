<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"comment_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"comment_read"})
     */
    private $content;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"comment_read"})
     */
    private $rating;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"comment_read"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"comment_read"})
     */
    private $recipe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

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

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }
}
