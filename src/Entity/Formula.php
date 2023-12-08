<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\FormulaRepository;

#[ORM\Entity(repositoryClass: FormulaRepository::class)]
class Formula
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column( length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\OneToMany(mappedBy: 'formula', targetEntity: ProductFormula::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $productFormulas;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'formulas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user;

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }


    public function __construct()
    {
        $this->productFormulas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;
        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Collection<int, ProductFormula>
     */
    public function getProductFormulas(): Collection
    {
        return $this->productFormulas;
    }

    public function addProductFormula(ProductFormula $productFormula): self
    {
        if (!$this->productFormulas->contains($productFormula)) {
            $this->productFormulas->add($productFormula);
            $productFormula->setFormula($this);
        }

        return $this;
    }

    public function removeProductFormula(ProductFormula $productFormula): self
    {
        if ($this->productFormulas->removeElement($productFormula)) {
            if ($productFormula->getFormula() === $this) {
                $productFormula->setFormula(null);
            }
        }

        return $this;
    }

}
