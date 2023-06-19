<?php

namespace App\Entity;

use App\Repository\DietRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DietRepository::class)
 */
class Diet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"diet_browse"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"diet_browse"})
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Member::class, inversedBy="diets")
     */
    private $member;

    /**
     * @ORM\ManyToMany(targetEntity=Recipe::class, inversedBy="diets")
     */
    private $recipe;

    public function __construct()
    {
        $this->member = new ArrayCollection();
        $this->recipe = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMember(): Collection
    {
        return $this->member;
    }

    public function addMember(Member $member): self
    {
        if (!$this->member->contains($member)) {
            $this->member[] = $member;
        }

        return $this;
    }

    public function removeMember(Member $member): self
    {
        $this->member->removeElement($member);

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipe(): Collection
    {
        return $this->recipe;
    }

    public function addRecipe(Recipe $recipe): self
    {
        if (!$this->recipe->contains($recipe)) {
            $this->recipe[] = $recipe;
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        $this->recipe->removeElement($recipe);

        return $this;
    }
}
