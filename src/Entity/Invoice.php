<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
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
    private InvoiceStatus $paymentStatus;

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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'devis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeSessionId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

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

    public function getPaymentStatus(): InvoiceStatus
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus($paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;

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
}
