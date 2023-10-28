<?php

namespace App\Entity;

enum PaymentStatus: string
{
    case null = "";
    case Pending = "PENDING";
    case Paid = "PAID";
    case Partial = "PARTIAL";
    case Refunded = "REFUNDED";
}
