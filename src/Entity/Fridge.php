<?php

namespace App\Entity;
use symfony\Component\Serializer\Annotation\Groups;
use App\Repository\FridgeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FridgeRepository::class)
 */
class Fridge
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"fridge_browse"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"fridge_browse"})
     * @Groups({"fridge_ingredient_read"})
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Ingredient::class, inversedBy="fridges")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"fridge_browse"})
     * @Groups({"fridge_ingredient_read"})
     */
    private $ingredient;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="fridges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"fridge_ingredient_read"})
     */
    private $expireDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $User): self
    {
        $this->user = $User;

        return $this;
    }

    public function getExpireDate(): ?\DateTimeInterface
    {
        return $this->expireDate;
    }

    public function setExpireDate(?\DateTimeInterface $expireDate): self
    {
        $this->expireDate = $expireDate;

        return $this;
    }
}

