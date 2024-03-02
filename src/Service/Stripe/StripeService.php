<?php

namespace App\Service\Stripe;

use App\Entity\Devis;
use Stripe\Stripe;
use Stripe\Customer;
use App\Entity\Invoice;
use App\Entity\Society;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use App\Service\Stripe\StripeHelper;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{
    private StripeClient $stripe;
    private StripeHelper $stripeHelper;
    private $urlGenerator;
    private EntityManagerInterface $entityManager;
    private InvoiceRepository $invoiceRepository;

    public function __construct(StripeHelper $stripeHelper, UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager, InvoiceRepository $invoiceRepository)
    {
        $this->stripe = $stripeHelper->getClient();
        $this->stripeHelper = $stripeHelper;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->invoiceRepository = $invoiceRepository;
        Stripe::setApiKey($this->stripeHelper->getClient());
        Stripe::setApiVersion('2022-08-01');
    }

    public function createCustomer(Society $society)
    {
        if ($society->getStripeId() === null) {
            $customer = $this->stripe->customers->create([
                'name' => $society->getName()
            ]);
            $society->setStripeId($customer->id);
            $this->entityManager->flush();
        } else {
            $customer = $this->stripe->customers->retrieve($society->getStripeId());
        }
    }

    public function getCustomer($customerID)
    {
        $customer = $this->stripe->customers->retrieve($customerID);
        return $customer;
    }


    public function getBalance($customerID)
    {
        $customer =  $this->stripe->customers->retrieve($customerID);
        return ($customer->balance / 100);
    }

    public function updateBalance($customerID, $amount)
    {
        $customer =  $this->stripe->customers->retrieve($customerID);
        $this->stripe->customers->update($customerID, ['balance' => $customer->balance + $amount]);
    }

    public function retriveInvoice($invoiceId)
    {
        return $this->stripe->invoices->retrieve($invoiceId);
    }

    public function retriveCharge($chargeId)
    {
        return $this->stripe->charges->retrieve($chargeId);
    }

    public function retriveSession($sessionId)
    {
        return $this->stripe->checkout->sessions->retrieve($sessionId);
    }

    public function cancelSession($sessionId)
    {
        return $this->stripe->checkout->sessions->expire($sessionId);
    }

    public function createPaymentIntent(Invoice $invoice, $isDeposit = true)
    {
        $taxeValue = ($invoice->getDevis()->getTotalPrice() * $invoice->getDevis()->getTaxe()) / 100;

        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate('checkout_success', ['token' => $invoice->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' =>  $this->urlGenerator->generate('checkout_cancel', ['token' => $invoice->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
            'invoice_creation' => ["enabled" => true],
            'shipping_address_collection' => [
                'allowed_countries' => ['FR'],
            ],
            'payment_method_types' => ['card'],
            'metadata' => [
                'invoice_id' => $invoice->getId(),
                'type' => $isDeposit ? 'deposit' : 'invoice',
            ],
            'line_items' => [
                [
                    'price_data' => [
                        'product_data' => [
                            'name' => $invoice->getInvoiceNumber(),
                        ],
                        'currency' => 'eur',
                        'unit_amount_decimal' =>  !$isDeposit ? $invoice->getTotalPrice() * 100 : ($invoice->getDevis()->getTotalPrice() * ($invoice->getDevis()->getDepositPercentage() / 100) * 100) + ($taxeValue * ($invoice->getDevis()->getDepositPercentage() / 100) * 100)

                    ],
                    'quantity' => 1,
                ]
            ],
        ]);
        return $session;
    }

    public function createPaymentIntentDevis(Devis $devis)
    {
        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate('checkout_success', ['token' => $devis->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' =>  $this->urlGenerator->generate('checkout_cancel', ['token' => $devis->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
            'invoice_creation' => ["enabled" => true],
            'shipping_address_collection' => [
                'allowed_countries' => ['FR'],
            ],
            'payment_method_types' => ['card'],
            'metadata' => [
                'type' => $devis->getToken(),
            ],
            'line_items' => [
                [
                    'price_data' => [
                        'product_data' => [
                            'name' => $devis->getDevisNumber(),
                        ],
                        'currency' => 'eur',
                        'unit_amount_decimal' => 0,
                    ],
                    'quantity' => 1,
                ]
            ],
        ]);
        return $session;
    }
}
