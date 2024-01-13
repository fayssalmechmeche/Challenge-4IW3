<?php

namespace App\Service\Stripe;

use App\Entity\Society;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StripeHelper
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getClient()
    {
        $client = new StripeClient($this->parameterBag->get('stripe_secret_key'));
        return $client;
    }
}
