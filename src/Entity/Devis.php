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
    case Delayed = "DELAYED";
    case Refunded = "REFUNDED";
}

enum DepositStatus: string
{
    case NonExistant = 'NON_EXISTANT';
    case Prevu = 'PREVU';
    case Genere = 'GENERE';
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

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private ?string $totalPrice = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: true)]
    private ?string $totalDuePrice = null;

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

    #[ORM\Column(type: 'string', enumType: DepositStatus::class, nullable: true)]
    private ?DepositStatus $depositStatus = null;


    #[ORM\OneToMany(mappedBy: 'devis', targetEntity: ProductItem::class, orphanRemoval: true)]
    private Collection $productItems;

    #[ORM\OneToMany(mappedBy: 'devis', targetEntity: Invoice::class, orphanRemoval: true)]
    private Collection $invoices;

    #[ORM\OneToMany(mappedBy: 'devis', targetEntity: DevisProduct::class,cascade: ['persist'], orphanRemoval: true)]
    private Collection $devisProducts;

    #[ORM\OneToMany(mappedBy: 'devis', targetEntity: DevisFormula::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $devisFormulas;

    #[ORM\ManyToOne(targetEntity: Society::class, inversedBy: 'devis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Society $society;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $depositPercentage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidite = null;

    /**
     * @return Society|null
     */
    public function getSociety(): ?Society
    {
        return $this->society;
    }

    /**
     * @param Society|null $society
     */
    public function setSociety(?Society $society): void
    {
        $this->society = $society;
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

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getTotalDuePrice(): ?string
    {
        return $this->totalDuePrice;
    }

    public function setTotalDuePrice(?string $totalDuePrice): static
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
     * Get the value of dateValidite
     */
    public function getDateValidite(): ?\DateTimeInterface
    {
        return $this->dateValidite;
    }

    /**
     * Set the value of dateValidite
     *
     * @return  self
     */
    public function setDateValidite(?\DateTimeInterface $dateValidite): self
    {
        $this->dateValidite = $dateValidite;

        return $this;
    }

    public function getDepositStatus(): ?DepositStatus
    {
        return $this->depositStatus;
    }

    public function setDepositStatus(?DepositStatus $depositStatus): self
    {
        $this->depositStatus = $depositStatus;

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

    public function getDepositPercentage(): ?float
    {
        return $this->depositPercentage;
    }

    public function setDepositPercentage(?float $depositPercentage): self
    {
        $this->depositPercentage = $depositPercentage;
        return $this;
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

    /**
     * Met à jour le statut de paiement à DELAYED si la date actuelle dépasse la date de validité.
     */
    public function updatePaymentStatusBasedOnValidity(): void
    {
        $today = new \DateTime(); // Obtient la date d'aujourd'hui
        // Vérifie si la date de validité est dépassée et si le statut de paiement n'est ni DELAYED ni PAYED
        if ($this->dateValidite < $today && $this->paymentStatus !== PaymentStatus::Paid) {
            $this->paymentStatus = PaymentStatus::Delayed;
        }
    }

}
