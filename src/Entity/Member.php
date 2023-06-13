<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use symfony\Component\Serializer\Annotation\Groups;
use App\Repository\MemberRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MemberRepository::class)
 */
class Member
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"member_browse"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"member_browse"})
     */
    private $nickname;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"member_browse"})
     */
    private $isAdult;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="members")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"member_read"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Allergen::class, mappedBy="member")
     */
    private $allergens;

    /**
     * @ORM\ManyToMany(targetEntity=Diet::class, mappedBy="member")
     */
    private $diets;

    public function __construct()
    {
        $this->allergens = new ArrayCollection();
        $this->diets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function isIsAdult(): ?bool
    {
        return $this->isAdult;
    }

    public function setIsAdult(bool $isAdult): self
    {
        $this->isAdult = $isAdult;

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

    /**
     * @return Collection<int, Allergen>
     */
    public function getAllergens(): Collection
    {
        return $this->allergens;
    }

    public function addAllergen(Allergen $allergen): self
    {
        if (!$this->allergens->contains($allergen)) {
            $this->allergens[] = $allergen;
            $allergen->addMember($this);
        }

        return $this;
    }

    public function removeAllergen(Allergen $allergen): self
    {
        if ($this->allergens->removeElement($allergen)) {
            $allergen->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Diet>
     */
    public function getDiets(): Collection
    {
        return $this->diets;
    }

    public function addDiet(Diet $diet): self
    {
        if (!$this->diets->contains($diet)) {
            $this->diets[] = $diet;
            $diet->addMember($this);
        }

        return $this;
    }

    public function removeDiet(Diet $diet): self
    {
        if ($this->diets->removeElement($diet)) {
            $diet->removeMember($this);
        }

        return $this;
    }
}
