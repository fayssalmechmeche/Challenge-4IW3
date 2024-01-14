<?php

namespace App\Controller;

use App\Service\Stripe\StripeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardCatererController extends AbstractController
{
    #[Route('/dashboard/caterer', name: 'home_index')]
    #[IsGranted('ROLE_SOCIETY')]
    public function index(StripeService $stripeService): Response
    {
        $customerID = $this->getUser()->getSociety()->getStripeId();

        return $this->render('dashboard_caterer/index.html.twig', [
            'controller_name' => 'DashboardCatererController',
            "balance" => $stripeService->getBalance($customerID)
        ]);
    }
}
