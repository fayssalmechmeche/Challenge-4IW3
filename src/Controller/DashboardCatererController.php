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
    #[Route('/dashboard/caterer', name: 'home_index')]
    #[IsGranted('ROLE_SOCIETY')]
    public function index(
        StripeService $stripeService, 
        DevisRepository $devisRepository, 
        InvoiceRepository $invoiceRepository, 
        CustomerRepository $customerRepository): Response
    {
        $customerID = $this->getUser()->getSociety()->getStripeId();

        $user = $this->getUser();
        $devis = $devisRepository->findBy(['user' => $user]);//TODO Society
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

        $amountDevisMonth = $devisRepository->findAmountDevisForCurrentMonth($user);     
        $amountDevisPreviousMonth = $devisRepository->findAmountDevisForPreviousMonth($user);

        if ($amountDevisPreviousMonth != 0) {
            $differenceLastAndCurrentMonthDevis =  ($amountDevisMonth - $amountDevisPreviousMonth) / $amountDevisPreviousMonth * 100 ;
        } else {
            $differenceLastAndCurrentMonthDevis = 0;
        }

        $NbNewCustomersMonth = $customerRepository->findNewCustomersForCurrentMonth($user);     
        $NbNewCustomersPreviousMonth = $customerRepository->findNewCustomersForPreviousMonth($user);

        if ($NbNewCustomersPreviousMonth != 0) {
            $differenceLastAndCurrentMonthCustomers =  ($NbNewCustomersMonth - $NbNewCustomersPreviousMonth) / $NbNewCustomersPreviousMonth * 100 ;
        } else {
            $differenceLastAndCurrentMonthCustomers = 0;
        }

        return $this->render('dashboard_caterer/index.html.twig', [
            'controller_name' => 'DashboardCatererController',
            'balance' => $stripeService->getBalance($customerID),
            'lastInvoicePaid' => $invoiceRepository->findLastInvoiceAmountForUser($user),
            'totalBalance' => $devisRepository->findAmountInvoicePaid($user),
            'amountDevisMonth' => $amountDevisMonth,
            'differenceLastAndCurrentMonthDevis' => $differenceLastAndCurrentMonthDevis,
            // 'amountDevisPendingMonth' => $devisRepository->findAmountDevisPendingForCurrentMonth($user),
            'NbNewCustomersMonth' => $NbNewCustomersMonth,
            'differenceLastAndCurrentMonthCustomers' => $differenceLastAndCurrentMonthCustomers,
            'nbDevisPending' => $devisRepository->findDevisPending($user),
            'nbInvoicePending' => $devisRepository->findInvoicePending($user),
            'totalPriceByMonth' => $totalPriceByMonth, 
        ]);
    }
}
