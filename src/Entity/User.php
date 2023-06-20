<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_browse"})
     * @Groups({"member_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user_browse"})
     * @Groups({"member_read"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user_browse"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"user_browse"})
     * @Groups({"member_read"})
     * @Groups({"comment_read"})
     * @Groups({"recipe_read"})
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups({"user_browse"})
     * @Groups({"comment_read"})
     * @Groups({"recipe_read"})
     */
    private $picture;

    /**
     * @ORM\ManyToMany(targetEntity=Recipe::class, inversedBy="users")
     */
    private $favoriteRecipes;

    /**

     * @ORM\OneToMany(targetEntity=Fridge::class, mappedBy="user")
     */
    private $fridges;


  /**
     * @ORM\OneToMany(targetEntity=Cart::class, mappedBy="user")
     */
    private $carts;

    /** 
     * @ORM\OneToMany(targetEntity=Member::class, mappedBy="user")
     */
    private $members;

    /**
     * @ORM\OneToMany(targetEntity=RecipeList::class, mappedBy="user")
     */
    private $recipeLists;

    /**
     * @ORM\OneToMany(targetEntity=Recipe::class, mappedBy="user")
     */
    private $recipes;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user")
     */
    private $comments;

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
        $this->favoriteRecipes = new ArrayCollection();

        $this->fridges = new ArrayCollection();


        $this->carts = new ArrayCollection();

        $this->members = new ArrayCollection();
        $this->recipeLists = new ArrayCollection();
        $this->recipes = new ArrayCollection();
        $this->comments = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

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

    /**
     * @return Collection<int, Recipe>
     */
    public function getFavoriteRecipes(): Collection
    {
        return $this->favoriteRecipes;
    }

    public function addFavoriteRecipe(Recipe $favoriteRecipe): self
    {
        if (!$this->favoriteRecipes->contains($favoriteRecipe)) {
            $this->favoriteRecipes[] = $favoriteRecipe;
        }

        return $this;
    }

    public function removeFavoriteRecipe(Recipe $favoriteRecipe): self
    {
        $this->favoriteRecipes->removeElement($favoriteRecipe);

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
            $fridge->setUser($this);
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

    public function addCart(Cart $cart):self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts[] = $cart;
            $cart->setUser($this);
        }
      
      return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setUser($this);

        }

        return $this;
    }


    public function removeFridge(Fridge $fridge): self
    {
        if ($this->fridges->removeElement($fridge)) {
            // set the owning side to null (unless already changed)
            if ($fridge->getUser() === $this) {
                $fridge->setUser(null);
            }
        }
      return $this;
    }


    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getUser() === $this) {
                $cart->setUser(null);
            }
        }
      return $this;
    }

    public function removeMember(Member $member): self
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getUser() === $this) {
                $member->setUser(null);

            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RecipeList>
     */
    public function getRecipeLists(): Collection
    {
        return $this->recipeLists;
    }

    public function addRecipeList(RecipeList $recipeList): self
    {
        if (!$this->recipeLists->contains($recipeList)) {
            $this->recipeLists[] = $recipeList;
            $recipeList->setUser($this);
        }

        return $this;
    }

    public function removeRecipeList(RecipeList $recipeList): self
    {
        if ($this->recipeLists->removeElement($recipeList)) {
            // set the owning side to null (unless already changed)
            if ($recipeList->getUser() === $this) {
                $recipeList->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->setUser($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        if ($this->recipes->removeElement($recipe)) {
            // set the owning side to null (unless already changed)
            if ($recipe->getUser() === $this) {
                $recipe->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
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

