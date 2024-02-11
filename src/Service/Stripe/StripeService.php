<?php

namespace App\Service\Stripe;


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

    public function createPaymentIntent(Invoice $invoice, $isDeposit = false)
    {
        $deposit = 30; // get deposit from database;
        // if (!$invoice->getDevis()->getUser()->getStripeId()) {
        //     $customer = $this->stripeHelper->createCustomer($booking->getUser());
        // } else {
        //     $customer = $this->stripe->customers->retrieve($booking->getUser()->getStripeId());
        // }

        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate('checkout_success', ['token' => $invoice->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' =>  $this->urlGenerator->generate('checkout_cancel', ['token' => $invoice->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
            'invoice_creation' => ["enabled" => true],
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['FR'],
            ],
            'metadata' => [
                'order_id' => $invoice->getId(),
                // 'user_id' => $invoice->getUser()->getId(),
            ],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        //si la réservation est immédiate ou si la réservation est différée et que le client ne veut pas payer avec un acompte
                        // on lui demande de payer la totalité du montant
                        //sinon on lui demande de payer 30% du montant
                        'unit_amount_decimal' => !$isDeposit ? round($invoice->getTotalPrice() * 100) : round(($invoice->getTotalPrice() * (($deposit ?? 30) / 100)) * 100),
                    ],
                    'quantity' => 1,
                ],

            ],
        ]);
        return $session;
    }
}
