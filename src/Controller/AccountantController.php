<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Repository\DevisProductRepository;
use App\Repository\DevisRepository;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccountantController extends AbstractController
{
    #[Route('/accountant', name: 'app_accountant')]
    public function index(
        DevisRepository $devisRepository, 
        DevisProductRepository $devisProductRepository, 
        InvoiceRepository $invoiceRepository
    ): Response
    {

        $society = $this->getUser()->getSociety();
        $devis = $devisRepository->findBy(['society' => $society]);
        $totalPriceDevisByMonth = [];

        //Calcul du totalDuePrice des devis par mois
        foreach ($devis as $d) {
            $month = $d->getCreatedAt()->format('m-Y');
            if (!isset($totalPriceDevisByMonth[$month])) {
                $totalPriceDevisByMonth[$month] = 0;
            }
            $totalPriceDevisByMonth[$month] += $d->getTotalDuePrice();

        }
        // Trier par date
        uksort($totalPriceDevisByMonth, function ($a, $b) {
            $dateA = strtotime("01-$a");
            $dateB = strtotime("01-$b");
            return $dateA - $dateB;
        });


        // dd($invoiceRepository->findAllInvoiceAmountForSociety($society));
        $invoice = $invoiceRepository->findAllInvoiceAmountForSociety($society);//TODO Society
        $totalPriceByMonth = [];

        //Calcul du totalDuePrice des factures par mois
        foreach ($invoice as $i) {
            $month = $i->getCreatedAt()->format('m-Y');
            if (!isset($totalPriceByMonth[$month])) {
                $totalPriceByMonth[$month] = 0;
            }
            $totalPriceByMonth[$month] += $i->getTotalDuePrice();

        }
        // Trier par date
        uksort($totalPriceByMonth, function ($a, $b) {
            $dateA = strtotime("01-$a");
            $dateB = strtotime("01-$b");
            return $dateA - $dateB;
        });
        
        return $this->render('accountant/index.html.twig', [
            'devis' => $devisRepository->findBy(['society' => $society]),
            'customers' => $devisRepository->findAllCustomerWithOrdersAndTotalDuePrice($society),
            'products' => $devisProductRepository->findAllOrderProductBySociety($society),
            'nameMostSellProduct' => $devisProductRepository->findMostSoldProductBySociety($society),
            'nameLessSellProduct' => $devisProductRepository->findLessSoldProductBySociety($society),
            'customerWithHighestSpending' => $devisRepository->findCustomerWithHighestTotalOrdersAndHisTotalSpending($society),
            'customerWithLowestSpending' => $devisRepository->findCustomerWithLowestTotalOrdersAndHisTotalSpending($society),
            'totalBalance' => $devisRepository->findAmountInvoicePaid($society),
            'totalPriceByMonth' => $totalPriceByMonth,
        ]);
    }
}
