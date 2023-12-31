<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Ingredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"recipe_read"})
     * @Groups({"ingredient_read"})
     * @Groups({"ingredient_browse"})
     * @Groups({"ingredient_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups({"recipe_read"})
     * @Groups({"ingredient_read"})
     * @Groups({"ingredient_browse"})
     * @Groups({"ingredient_suggestion"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"ingredient_read"})
     */
    private $isCold;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"recipe_read"})
     * @Groups({"ingredient_read"})
     * @Groups({"ingredient_browse"})
     * @Groups({"ingredient_suggestion"})
     */
    private $unit;

    /**
     * @ORM\ManyToOne(targetEntity=Department::class, inversedBy="ingredients")
     * @Groups({"ingredient_read"})
     */
    private $department;

    /**
     * @ORM\OneToMany(targetEntity=ContainsIngredient::class, mappedBy="ingredient")
     */
    private $containsIngredients;

    /**
     * @ORM\OneToMany(targetEntity=Fridge::class, mappedBy="ingredient")
     */
    private $fridges;

    /**
     * @ORM\OneToMany(targetEntity=Cart::class, mappedBy="ingredient")
     * 
     */
    private $carts;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;


    public function __construct()
    {
        $this->containsIngredients = new ArrayCollection();

        $this->fridges = new ArrayCollection();

        $this->carts = new ArrayCollection();

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

    public function isIsCold(): ?bool
    {
        return $this->isCold;
    }

    public function setIsCold(bool $isCold): self
    {
        $this->isCold = $isCold;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

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
            $containsIngredient->setIngredient($this);
        }

        return $this;
    }

    public function removeContainsIngredient(ContainsIngredient $containsIngredient): self
    {
        if ($this->containsIngredients->removeElement($containsIngredient)) {
            // set the owning side to null (unless already changed)
            if ($containsIngredient->getIngredient() === $this) {
                $containsIngredient->setIngredient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Fridge>
     */
    public function getFridges(): Collection
    {
        return $this->fridges;
    }

    public function addFridge(Fridge $fridge): self
    {
        if (!$this->fridges->contains($fridge)) {
            $this->fridges[] = $fridge;
            $fridge->setIngredient($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts[] = $cart;
            $cart->setIngredient($this);
        }

        return $this;
    }


    public function removeFridge(Fridge $fridge): self
    {
        if ($this->fridges->removeElement($fridge)) {
            // set the owning side to null (unless already changed)
            if ($fridge->getIngredient() === $this) {
                $fridge->setIngredient(null);
            }
        }
      return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getIngredient() === $this) {
                $cart->setIngredient(null);

            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime("now");
    }
}
