<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\DevisRepository;
use App\Repository\InvoiceRepository;
use App\Service\Stripe\StripeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardCatererController extends AbstractController
{
    #[Route('/dashboard', name: 'home_index')]
    public function index(
        StripeService $stripeService, 
        DevisRepository $devisRepository, 
        InvoiceRepository $invoiceRepository, 
        CustomerRepository $customerRepository): Response
    {
        $customerID = $this->getUser()->getSociety()->getStripeId();

        $society = $this->getUser()->getSociety();
        $devis = $devisRepository->findBy(['society' => $society]);
        $totalPriceByMonth = [];

        //Calcul du totalDuePrice des devis par mois
        foreach ($devis as $d) {
            $month = $d->getCreatedAt()->format('m-Y');
            if (!isset($totalPriceByMonth[$month])) {
                $totalPriceByMonth[$month] = 0;
            }
            $totalPriceByMonth[$month] += $d->getTotalDuePrice();

        }
        // Trier par date
        uksort($totalPriceByMonth, function ($a, $b) {
            $dateA = strtotime("01-$a");
            $dateB = strtotime("01-$b");
            return $dateA - $dateB;
        });

        $amountDevisMonth = $devisRepository->findAmountDevisForCurrentMonth($society);     
        $amountDevisPreviousMonth = $devisRepository->findAmountDevisForPreviousMonth($society);

        if ($amountDevisPreviousMonth != 0) {
            $differenceLastAndCurrentMonthDevis =  ($amountDevisMonth / $amountDevisPreviousMonth) * 100 ;
        } else {
            $differenceLastAndCurrentMonthDevis = 0;
        }

        $NbNewCustomersMonth = $customerRepository->findNewCustomersForCurrentMonth($society);     
        $NbNewCustomersPreviousMonth = $customerRepository->findNewCustomersForPreviousMonth($society);
        
        if ($NbNewCustomersPreviousMonth != 0) {
            $differenceLastAndCurrentMonthCustomers =  ($NbNewCustomersMonth / $NbNewCustomersPreviousMonth) * 100 ;
        } else {
            $differenceLastAndCurrentMonthCustomers = 0;
        }
        
        return $this->render('dashboard_caterer/index.html.twig', [
            'controller_name' => 'DashboardCatererController',
            'balance' => $stripeService->getBalance($customerID),
            'lastInvoicePaid' => $invoiceRepository->findLastInvoiceAmountForSociety($society),
            'totalBalance' => $devisRepository->findAmountInvoicePaid($society),
            'amountDevisMonth' => $amountDevisMonth,
            'differenceLastAndCurrentMonthDevis' => $differenceLastAndCurrentMonthDevis,
            'NbNewCustomersMonth' => $NbNewCustomersMonth,
            'differenceLastAndCurrentMonthCustomers' => $differenceLastAndCurrentMonthCustomers,
            'nbDevisPending' => $devisRepository->findDevisPending($society),
            'nbInvoicePending' => $devisRepository->findInvoicePending($society),
            'totalPriceByMonth' => $totalPriceByMonth, 
        ]);
    }
}
