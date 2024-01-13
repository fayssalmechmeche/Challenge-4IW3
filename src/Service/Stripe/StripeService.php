<?php

namespace App\Service\Stripe;


use Stripe\Stripe;
use Stripe\Customer;
use App\Entity\Society;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use App\Service\Stripe\StripeHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{
    private StripeClient $stripe;
    private StripeHelper $stripeHelper;
    private $urlGenerator;
    private EntityManagerInterface $entityManager;

    public function __construct(StripeHelper $stripeHelper, UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager)
    {
        $this->stripe = $stripeHelper->getClient();
        $this->stripeHelper = $stripeHelper;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
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
}
