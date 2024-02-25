<?php

namespace App\Twig\Extension;

use App\Entity\InvoiceStatus;
use App\Twig\Runtime\EnumRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class EnumExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('translateInvoiceStatus', [EnumRuntime::class, 'translateInvoiceStatus']),
        ];
    }
}
