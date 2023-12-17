<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(type: "string")]
    private ?string $productCategory = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Society $society = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductFormula::class, orphanRemoval: true)]
    private Collection $productFormulas;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: DevisProduct::class)]
    private Collection $devisProducts;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'products')]
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
        $this->devisProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getProductCategory(): ?string
    {
        return $this->productCategory;
    }

    public function setProductCategory(?string $productCategory): self
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    public function getSociety(): ?Society
    {
        return $this->society;
    }

    public function setSociety(?Society $society): static
    {
        $this->society = $society;

        return $this;
    }

    /**
     * @return Collection<int, ProductFormula>
     */
    public function getProductFormulas(): Collection
    {
        return $this->productFormulas;
    }

    public function addProductFormula(ProductFormula $productFormula): static
    {
        if (!$this->productFormulas->contains($productFormula)) {
            $this->productFormulas->add($productFormula);
            $productFormula->setProduct($this);
        }

        return $this;
    }

    public function removeProductFormula(ProductFormula $productFormula): static
    {
        if ($this->productFormulas->removeElement($productFormula)) {
            // set the owning side to null (unless already changed)
            if ($productFormula->getProduct() === $this) {
                $productFormula->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DevisProduct>
     */
    public function getDevisProducts(): Collection
    {
        return $this->devisProducts;
    }

    public function addDevisProduct(DevisProduct $devisProduct): static
    {
        if (!$this->devisProducts->contains($devisProduct)) {
            $this->devisProducts->add($devisProduct);
            $devisProduct->setProduct($this);
        }

        return $this;
    }

    public function removeDevisProduct(DevisProduct $devisProduct): static
    {
        if ($this->devisProducts->removeElement($devisProduct)) {
            // set the owning side to null (unless already changed)
            if ($devisProduct->getProduct() === $this) {
                $devisProduct->setProduct(null);
            }
        }

        return $this;
    }
}
