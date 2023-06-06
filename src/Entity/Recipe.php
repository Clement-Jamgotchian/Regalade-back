<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecipeRepository::class)
 */
class Recipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="integer")
     */
    private $cookingDuration;

    /**
     * @ORM\Column(type="integer")
     */
    private $setupDuration;

    /**
     * @ORM\Column(type="text")
     */
    private $step;

    /**
     * @ORM\Column(type="smallint")
     */
    private $difficulty;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="recipes")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=ContainsIngredient::class, mappedBy="recipe")
     */
    private $containsIngredients;

    public function __construct()
    {
        $this->containsIngredients = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCookingDuration(): ?int
    {
        return $this->cookingDuration;
    }

    public function setCookingDuration(int $cookingDuration): self
    {
        $this->cookingDuration = $cookingDuration;

        return $this;
    }

    public function getSetupDuration(): ?int
    {
        return $this->setupDuration;
    }

    public function setSetupDuration(int $setupDuration): self
    {
        $this->setupDuration = $setupDuration;

        return $this;
    }

    public function getStep(): ?string
    {
        return $this->step;
    }

    public function setStep(string $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): self
    {
        $this->difficulty = $difficulty;

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

    /**
     * @return Collection<int, ContainsIngredient>
     */
    public function getContainsIngredients(): Collection
    {
        return $this->containsIngredients;
    }

    public function addContainsIngredient(ContainsIngredient $containsIngredient): self
    {
        if (!$this->containsIngredients->contains($containsIngredient)) {
            $this->containsIngredients[] = $containsIngredient;
            $containsIngredient->setRecipe($this);
        }

        return $this;
    }

    public function removeContainsIngredient(ContainsIngredient $containsIngredient): self
    {
        if ($this->containsIngredients->removeElement($containsIngredient)) {
            // set the owning side to null (unless already changed)
            if ($containsIngredient->getRecipe() === $this) {
                $containsIngredient->setRecipe(null);
            }
        }

        return $this;
    }
}
