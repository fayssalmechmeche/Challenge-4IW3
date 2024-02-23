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
        ProductRepository $productRepository,
        CustomerRepository $customerRepository,
        InvoiceRepository $invoiceRepository
    ): Response
    {

        $user = $this->getUser();
        $devis = $devisRepository->findAll();//TODO Society
        $totalPriceDevisByMonth = [];

        //Calcul du totalDuePrice des devis par mois
        foreach ($devis as $d) {
            $month = $d->getCreatedAt()->format('m-Y');
            if (!isset($totalPriceDevisByMonth[$month])) {
                $totalPriceDevisByMonth[$month] = 0;
            }
            $totalPriceDevisByMonth[$month] += $d->getTotalDuePrice();

        }
        ksort($totalPriceDevisByMonth); //Trie par date

        $invoice = $invoiceRepository->findBy(['paymentStatus' => 'PAID']);//TODO Society
        $totalPriceByMonth = [];

        //Calcul du totalDuePrice des devis par mois
        foreach ($invoice as $i) {
            $month = $i->getCreatedAt()->format('m-Y');
            if (!isset($totalPriceByMonth[$month])) {
                $totalPriceByMonth[$month] = 0;
            }
            $totalPriceByMonth[$month] += $i->getTotalDuePrice();

        }
        ksort($totalPriceByMonth); //Trie par date
        
        // dd($devisRepository->findAmountInvoicePaid());
        return $this->render('accountant/index.html.twig', [
            'devis' => $devisRepository->findBy(['user' => $user]),
            'customers' => $devisRepository->findAllCustomerWithOrdersAndTotalDuePrice(),
            'products' => $devisProductRepository->findAllOrderProductByUser(),
            'nameMostSellProduct' => $devisProductRepository->findMostSoldProductByUser(),
            'nameLessSellProduct' => $devisProductRepository->findLessSoldProductByUser(),
            'customerWithHighestSpending' => $devisRepository->findCustomerWithHighestTotalOrdersAndHisTotalSpending(),
            'customerWithLowestSpending' => $devisRepository->findCustomerWithLowestTotalOrdersAndHisTotalSpending(),
            'totalBalance' => $devisRepository->findAmountInvoicePaid(),
            'totalPriceByMonth' => $totalPriceByMonth,
        ]);
    }
}
