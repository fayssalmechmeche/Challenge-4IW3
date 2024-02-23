<?php

namespace App\Twig\Runtime;

use App\Entity\InvoiceStatus;
use Twig\Extension\RuntimeExtensionInterface;

class EnumRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function translateInvoiceStatus($value): string
    {
        return match ($value) {
            InvoiceStatus::Pending => 'En attente',
            InvoiceStatus::Paid => 'Payé',
            InvoiceStatus::Canceled => 'Annulé',
            InvoiceStatus::Refused => 'Refusé',
            InvoiceStatus::Partial => 'Partiel',
            InvoiceStatus::Refunded => 'Remboursé',
            InvoiceStatus::null => 'En attente de validation de la facture',
            default => 'Aucun',
        };
    }
}
