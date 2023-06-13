<?php

namespace App\Entity;

use App\Repository\RecipeListRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecipeListRepository::class)
 */
class RecipeList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"recipeList_browse"})
     */
    private $portions;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="recipeLists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="recipeLists")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"recipeList_browse"})
     */
    private $recipe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPortions(): ?int
    {
        return $this->portions;
    }

    public function setPortions(?int $portions): self
    {
        $this->portions = $portions;

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
