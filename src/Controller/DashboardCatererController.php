<?php

namespace App\Controller;

use App\Repository\DevisRepository;
use App\Service\Stripe\StripeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardCatererController extends AbstractController
{
    #[Route('/dashboard/caterer', name: 'home_index')]
    #[IsGranted('ROLE_SOCIETY')]
    public function index(StripeService $stripeService, DevisRepository $devisRepository): Response
    {
        $customerID = $this->getUser()->getSociety()->getStripeId();

        $user = $this->getUser();
        $devis = $devisRepository->findAll();//TODO Society
        $totalPriceByMonth = [];

        //Calcul du totalDuePrice des devis par mois
        foreach ($devis as $d) {
            $month = $d->getCreatedAt()->format('m-Y');
            if (!isset($totalPriceByMonth[$month])) {
                $totalPriceByMonth[$month] = 0;
            }
            $totalPriceByMonth[$month] += $d->getTotalDuePrice();

        }
        ksort($totalPriceByMonth); //Trie par date*

        $amountDevisMonth = $devisRepository->findAmountDevisPendingForCurrentMonth();
        $amountDevisMonth['totalDuePrice'] ?? $amountDevisMonth['totalDuePrice'] = 0;

        // dd($amountDevisMonth);

        return $this->render('dashboard_caterer/index.html.twig', [
            'controller_name' => 'DashboardCatererController',
            'balance' => $stripeService->getBalance($customerID),
            'totalBalance' => $devisRepository->findAmountInvoicePaid(),
            'AmountDevisMonth' => $amountDevisMonth,
            'nbDevisPending' => $devisRepository->findDevisPending(),
            'nbInvoicePending' => $devisRepository->findInvoicePending(),
            'totalPriceByMonth' => $totalPriceByMonth, 
        ]);
    }
}
