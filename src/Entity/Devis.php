<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

 enum PaymentStatus: string
{
    case null = "";
    case Pending = "PENDING";
    case Paid = "PAID";
    case Partial = "PARTIAL";
    case Refunded = "REFUNDED";
}

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $taxe = null;

    #[ORM\Column]
    private ?int $totalPrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalDuePrice = null;

    #[ORM\Column(nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(type: "string", enumType: PaymentStatus::class, nullable: true)]
    private PaymentStatus $paymentStatus;

    #[ORM\Column(type: Types::STRING, length: 20)]
    private ?string $devisNumber = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;



    #[ORM\ManyToOne(inversedBy: 'devis', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;


    #[ORM\OneToMany(mappedBy: 'devis', targetEntity: ProductItem::class, orphanRemoval: true)]
    private Collection $productItems;

    #[ORM\OneToMany(mappedBy: 'devis', targetEntity: Invoice::class, orphanRemoval: true)]
    private Collection $invoices;

    #[ORM\OneToMany(mappedBy: 'devis', targetEntity: DevisProduct::class,cascade: ['persist'], orphanRemoval: true)]
    private Collection $devisProducts;

    #[ORM\OneToMany(mappedBy: 'devis', targetEntity: DevisFormula::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $devisFormulas;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'devis')]
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
        $this->productItems = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->paymentStatus = PaymentStatus::Pending;
        $this->devisProducts = new ArrayCollection();
        $this->devisFormulas = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaxe(): ?int
    {
        return $this->taxe;
    }

    public function setTaxe(?int $taxe): static
    {
        $this->taxe = $taxe;

        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getTotalDuePrice(): ?int
    {
        return $this->totalDuePrice;
    }

    public function setTotalDuePrice(?int $totalDuePrice): static
    {
        $this->totalDuePrice = $totalDuePrice;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of paymentStatus
     */
    public function getPaymentStatus(): PaymentStatus
    {
        return $this->paymentStatus;
    }

    /**
     * Set the value of paymentStatus
     *
     * @return  self
     */
    public function setPaymentStatus($paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }


    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, ProductItem>
     */
    public function getProductItems(): Collection
    {
        return $this->productItems;
    }

    public function addProductItem(ProductItem $productItem): static
    {
        if (!$this->productItems->contains($productItem)) {
            $this->productItems->add($productItem);
            $productItem->setDevis($this);
        }

        return $this;
    }

    public function setDevisNumber(string $devisNumber): self
    {
        $this->devisNumber = $devisNumber;

        return $this;
    }

    public function getDevisNumber(): ?string
    {
        return $this->devisNumber;
    }

    public function addDevisProduct(DevisProduct $devisProduct): self
    {
        if (!$this->devisProducts->contains($devisProduct)) {
            $this->devisProducts[] = $devisProduct;
            $devisProduct->setDevis($this);
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     */
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function removeDevisProduct(DevisProduct $devisProduct): self
    {
        if ($this->devisProducts->removeElement($devisProduct)) {
            // set the owning side to null (unless already changed)
            if ($devisProduct->getDevis() === $this) {
                $devisProduct->setDevis(null);
            }
        }

        return $this;
    }

    public function addDevisFormula(DevisFormula $devisFormula): self
    {
        if (!$this->devisFormulas->contains($devisFormula)) {
            $this->devisFormulas[] = $devisFormula;
            $devisFormula->setDevis($this);
        }

        return $this;
    }

    public function removeDevisFormula(DevisFormula $devisFormula): self
    {
        if ($this->devisFormulas->removeElement($devisFormula)) {
            // set the owning side to null (unless already changed)
            if ($devisFormula->getDevis() === $this) {
                $devisFormula->setDevis(null);
            }
        }

        return $this;
    }


    public function removeProductItem(ProductItem $productItem): static
    {
        if ($this->productItems->removeElement($productItem)) {
            // set the owning side to null (unless already changed)
            if ($productItem->getDevis() === $this) {
                $productItem->setDevis(null);
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

    /**
     * @return Collection<int, DevisFormula>
     */
    public function getDevisFormulas(): Collection
    {
        return $this->devisFormulas;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setDevis($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            if ($invoice->getDevis() === $this) {
                $invoice->setDevis(null);
            }
        }

        return $this;
    }
}
