<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
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
     * @Groups({"recipe_browse"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups({"recipe_browse"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"recipe_browse"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups({"recipe_browse"})
     */
    private $picture;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"recipe_browse"})
     */
    private $cookingDuration;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"recipe_browse"})
     */
    private $setupDuration;

    /**
     * @ORM\Column(type="text")
     * @Groups({"recipe_read"})
     */
    private $step;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"recipe_browse"})
     */
    private $difficulty;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="recipes")
     * @Groups({"recipe_browse"})
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=ContainsIngredient::class, mappedBy="recipe")
     * @Groups({"recipe_read"})
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