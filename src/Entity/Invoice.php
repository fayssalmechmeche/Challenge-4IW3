<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

enum InvoiceType: string
{
    case null = "";
    case Deposit = "DEPOSIT";
    case Invoice = "STANDARD";
}

enum InvoiceStatus: string
{
    case null = "";
    case Pending = "PENDING";
    case Paid = "PAID";
    case Refused = "REFUSED";
    case Partial = "PARTIAL";
    case Delayed = "DELAYED";
    case Refunded = "REFUNDED";
    case Canceled = "CANCELED";
}

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", enumType: InvoiceStatus::class, nullable: true)]
    private InvoiceStatus $invoiceStatus;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devis $devis = null;

    #[ORM\Column]
    private ?int $taxe = null;

    #[ORM\Column(type: "string", enumType: InvoiceType::class, nullable: true)]
    private InvoiceType $invoiceType;

    #[ORM\Column]
    private ?int $totalPrice = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $invoiceNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $totalDuePrice = null;

    #[ORM\Column(length: 255)]
    private ?string $remise = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $paymentDueTime = null;

    #[ORM\ManyToOne(targetEntity: Society::class, inversedBy: 'devis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Society $society;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeSessionId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidite = null;
    public function __construct()
    {

        $this->invoiceStatus = InvoiceStatus::Pending;
    }

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): static
    {
        $this->devis = $devis;

        return $this;
    }

    public function getTaxe(): ?int
    {
        return $this->taxe;
    }

    public function setTaxe(int $taxe): static
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

    public function getInvoiceStatus(): InvoiceStatus
    {
        return $this->invoiceStatus;
    }

    public function setInvoiceStatus($invoiceStatus): self
    {
        $this->invoiceStatus = $invoiceStatus;

        return $this;
    }


    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): static
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getTotalDuePrice(): ?string
    {
        return $this->totalDuePrice;
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

    public function setTotalDuePrice(string $totalDuePrice): static
    {
        $this->totalDuePrice = $totalDuePrice;

        return $this;
    }

    public function getRemise(): ?string
    {
        return $this->remise;
    }

    public function setRemise(string $remise): static
    {
        $this->remise = $remise;

        return $this;
    }

    public function getPaymentDueTime(): ?\DateTimeInterface
    {
        return $this->paymentDueTime;
    }

    public function setPaymentDueTime(\DateTimeInterface $paymentDueTime): static
    {
        $this->paymentDueTime = $paymentDueTime;

        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(?string $stripeSessionId): static
    {
        $this->stripeSessionId = $stripeSessionId;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getInvoiceType(): InvoiceType
    {
        return $this->invoiceType;
    }

    public function setInvoiceType(?InvoiceType $invoiceType): static
    {
        $this->invoiceType = $invoiceType;

        return $this;
    }

    /**
     * Met à jour le statut de paiement à DELAYED si la date actuelle dépasse la date de validité.
     */
    public function updateInvoiceStatusBasedOnValidity(): void
    {
        $today = new \DateTime(); // Obtient la date d'aujourd'hui
        // Vérifie si la date de validité est dépassée et si le statut de paiement n'est ni DELAYED ni PAYED
        if ($this->dateValidite < $today && $this->invoiceStatus !== InvoiceStatus::Paid) {
            $this->invoiceStatus = InvoiceStatus::Delayed;
        }
    }
}
