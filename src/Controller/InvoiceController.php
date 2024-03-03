<?php

namespace App\Controller;

use DateTime;
use App\Entity\Invoice;
use App\Entity\InvoiceType;
use Psr\Log\LoggerInterface;
use App\Entity\InvoiceStatus;

use Sabberworm\CSS\Value\URL;
use App\Service\MailjetService;
use App\Repository\DevisRepository;
use App\Repository\InvoiceRepository;
use App\Service\Stripe\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/invoice')]
#[IsGranted('ROLE_SOCIETY')]
class InvoiceController extends AbstractController
{
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository, EntityManagerInterface $entityManager): Response
    {
        $society = $this->getSociety(); // Récupère l'utilisateur connecté

        if ($society) {
            // Récupère uniquement les factures associées à la société connectée
            $allInvoice = $invoiceRepository->findBy(['society' => $society]);

            // Met à jour le statut de paiement si nécessaire pour chaque facture récupérée
            foreach ($allInvoice as $invoice) {
                $invoice->updateInvoiceStatusBasedOnValidity();
                $invoiceStatus = $invoice->getInvoiceStatus()->value; // Assurez-vous d'accéder correctement à la valeur de l'énumération
                $invoice->translatedStatus = $this->translateInvoiceStatus($invoiceStatus);
            }

            // Sauvegarde les modifications dans la base de données
            $entityManager->flush();
        } else {
            // Si aucune société n'est connectée, définit un tableau vide
            $allInvoice = [];
        }

        // Retourne les factures filtrées à la vue
        return $this->render('invoice/index.html.twig', [
            'invoices' => $allInvoice,
        ]);
    }


    #[Route('/api', name: 'api_invoice_devis_index', methods: ['GET'])]
    public function apiIndex(DevisRepository $devisRepository): Response
    {
        $society = $this->getSociety(); // Récupère l'utilisateur connecté

        if ($society) {
            $devis = $devisRepository->findPendingByUser($society);
        } else {
            return $this->json([]); // Retourne une réponse vide si aucun utilisateur n'est connecté
        }

        $data = [];
        foreach ($devis as $devi) {
            $customer = $devi->getCustomer();
            $customerName = '';

            if ($customer) {
                $customerName = $customer->getNameSociety() ?: $customer->getName() . ' ' . $customer->getLastName();
            }

            $data[] = [
                'id' => $devi->getId(),
                'devisNumber' => $devi->getDevisNumber(),
                'createdAt' => $devi->getCreatedAt() ? $devi->getCreatedAt()->format('Y-m-d') : '',
                'customer' => $customerName,
                'depositStatus' => $devi->getDepositStatus() ? $devi->getDepositStatus()->value : 'NON_EXISTANT',
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/invoices', name: 'api_invoices_list', methods: ['GET'])]
    public function listInvoices(InvoiceRepository $invoiceRepository): JsonResponse
    {
        $invoices = $invoiceRepository->findAll();
        $data = [];


        foreach ($invoices as $invoice) {
            $devis = $invoice->getDevis(); // Récupère l'entité Devis liée à la facture
            $devisNumber = $devis ? $devis->getDevisNumber() : null;
            $data[] = [
                'id' => $invoice->getId(),
                'devisNumber' => $devisNumber,
                'invoiceType' => $invoice->getInvoiceType(),
                'invoiceNumber' => $invoice->getInvoiceNumber(),
                'dateValidite' => $invoice->getDateValidite(),
                'status' => $invoice->getInvoiceStatus()


                // Ajoutez d'autres propriétés de l'entité Facture selon vos besoins
            ];
        }

        return $this->json($data);
    }


    #[Route('/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, InvoiceRepository $invoiceRepository, DevisRepository $devisRepository, MailjetService $mailjetService): Response
    {
        $devisId = $request->query->get('devisId');
        $deposit = $request->query->get('deposit');
        $invoice = new Invoice();


        $society = $this->getSociety();
        $lastInvoiceNumber = $invoiceRepository->findLastinvoiceNumberForUser($society);
        $newInvoiceNumber = $this->generateNewinvoiceNumber($lastInvoiceNumber);
        $invoice->setSociety($society);
        $devis = $devisRepository->find($devisId);
        $invoice->setDevis($devis);
        $society = $this->getSociety();
        $userEmail = $society ? $society->getEmail() : '';

        $taxeValue = ($devis->getTotalPrice() * $devis->getTaxe()) / 100;
        $totalTTC = $devis->getTotalPrice() + $taxeValue;
        $depositAmount = ($devis->getTotalPrice() * ($devis->getDepositPercentage() / 100)) + ($taxeValue * ($devis->getDepositPercentage() / 100));
        $new = $totalTTC;

        if ($request->isMethod('POST')) {
            $invoice->setInvoiceNumber($newInvoiceNumber);
            $invoice->setTaxe($taxeValue);
            $invoice->setTotalPrice(round($new));
            $invoice->setTotalDuePrice(round($new));
            // Supposons que $deposit est une chaîne de caractères qui détermine le type de la facture
            $invoice->setInvoiceType($deposit == "true" ? InvoiceType::Deposit : InvoiceType::Invoice);
            $invoice->setRemise(0);
            $invoice->setInvoiceStatus(InvoiceStatus::Pending);
            $invoice->setToken(uniqid('invoice_'));
            $paymentDueTime = new DateTime('now + 10 days');
            $invoice->setPaymentDueTime($paymentDueTime);
            $createdAt = new DateTime();
            $invoice->setCreatedAt($createdAt);
            $dateValidite = new DateTime($request->request->get('dateValidite'));
            $invoice->setDateValidite($dateValidite);
            $entityManager->persist($invoice);
            $entityManager->flush();
            $deposit == "true" ? $this->generateUrl('checkout_index', ['token' => $invoice->getToken(), 'deposit' => true], UrlGeneratorInterface::ABSOLUTE_URL) : $this->generateUrl('checkout_index', ['token' => $invoice->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
            $mailjetService->sendEmail(
                $invoice->getDevis()->getCustomer()->getEmail(),
                $invoice->getDevis()->getCustomer()->getNameSociety() ? $invoice->getDevis()->getCustomer()->getNameSociety() : $invoice->getDevis()->getCustomer()->getName() . ' ' . $invoice->getDevis()->getCustomer()->getLastName(),
                MailjetService::TEMPLATE_INVOICE_NO_DEPOSIT,
                [
                    'firstName' => $invoice->getDevis()->getCustomer()->getNameSociety() ? $invoice->getDevis()->getCustomer()->getNameSociety() :  $invoice->getDevis()->getCustomer()->getName(),
                    'name' => $invoice->getDevis()->getCustomer()->getNameSociety() ? '' : $invoice->getDevis()->getCustomer()->getLastName(),
                    'invoice_link' => $deposit == "true" ? $this->generateUrl('checkout_index', ['token' => $invoice->getToken(), 'deposit' => true], UrlGeneratorInterface::ABSOLUTE_URL) : $this->generateUrl('checkout_index', ['token' => $invoice->getToken()], UrlGeneratorInterface::ABSOLUTE_URL)
                ]
            );
            $this->addFlash('success', 'La facture a été créée avec succès et est en attente de paiement par le client.');
            return $this->redirectToRoute('app_invoice_index');
        }

        return $this->render('invoice/new.html.twig', [
            'invoice' => $invoice,
            'userEmail' => $userEmail,
            'invoiceNumber' => $newInvoiceNumber,
            'totalTTC' => $totalTTC,
            'totalHT' => $devis->getTotalPrice(),
            'taxeValue' => $taxeValue,
            'depositAmount' => $depositAmount,
            'newTotalDuePrice' => $new,
            'society' => $society,
            'devis' => $devis


        ]);
    }


    #[Route('/devisDetail', name: 'invoice_devis_detail', methods: ['GET', 'POST'])]
    public function devisDetail(Request $request, DevisRepository $devisRepository): Response
    {
        $devisId = $request->query->get('devisId');
        $devis = $devisRepository->find($devisId);

        if (!$devis) {
            return $this->json(['error' => 'Devis not found'], Response::HTTP_NOT_FOUND);
        }

        $devisProducts = [];
        foreach ($devis->getDevisProducts() as $product) {
            $devisProducts[] = [
                'name' => $product->getProduct()->getName(),
                'quantity' => $product->getQuantity(),
                'price' => $product->getPrice(),
            ];
        }

        $devisFormulas = [];
        foreach ($devis->getDevisFormulas() as $formula) {
            $devisFormulas[] = [
                'name' => $formula->getFormula()->getName(),
                'quantity' => $formula->getQuantity(),
                'price' => $formula->getPrice(),
            ];
        }
        return $this->json([
            'devisProducts' => $devisProducts,
            'devisFormulas' => $devisFormulas,
        ]);
    }

    #[Route('/{id}', name: 'app_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        $society = $this->getSociety();
        if ($society->getId() != $invoice->getSociety()->getId()) {
            return $this->redirectToRoute('app_invoice_index');
        }

        $devis = $invoice->getDevis();
        $invoiceNumber = $invoice->getInvoiceNumber();
        $totalTTC = $invoice->getTotalDuePrice();
        $totalHT = $invoice->getTotalPrice();
        $taxe = $devis->getTaxe();
        $new = $totalTTC;
        $userEmail = $society ? $society->getEmail() : '';
        $depositAmount = ($devis->getTotalPrice() * ($devis->getDepositPercentage() / 100)) + ($taxe * ($devis->getDepositPercentage() / 100));

        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
            'userEmail' => $userEmail,
            'invoiceNumber' => $invoiceNumber,
            'totalTTC' => $totalTTC,
            'totalHT' => $totalHT,
            'taxeValue' => $taxe,
            'depositAmount' => $depositAmount,
            'newTotalDuePrice' => $new,
            'society' => $society,
            'devis' => $devis
        ]);
    }



    #[Route('/{id}', name: 'app_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $society = $this->getSociety();
        if ($society->getId() != $invoice->getSociety()->getId()) {
            return $this->redirectToRoute('app_invoice_index');
        }

        $entityManager->remove($invoice);
        $entityManager->flush();

        $this->addFlash('success', 'La facture a bien été supprimé.');
        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }

    private function generateNewInvoiceNumber(?string $lastInvoiceNumber): string
    {
        $year = date('Y');
        $month = date('m');
        $sequentialNumber = 1;

        if ($lastInvoiceNumber) {
            // Extrait le numéro séquentiel du dernier numéro de devis et l'incrémente
            $lastParts = explode('-', $lastInvoiceNumber);
            $lastSequential = (int)end($lastParts);
            $sequentialNumber = $lastSequential + 1;
        }

        return sprintf("%s-%s-%04d", $year, $month, $sequentialNumber);
    }

    public function getSociety()
    {
        // Exemple de récupération de l'utilisateur courant et de sa société
        $user = $this->getUser(); // Supposons que `getUser()` retourne l'utilisateur courant
        if ($user) {
            return $user->getSociety(); // Supposons que l'utilisateur a une méthode `getSociety()`
        }
        return null; // ou gérer autrement si l'utilisateur n'est pas connecté ou n'a pas de société
    }

    public function translateInvoiceStatus(string $status): string
    {
        switch ($status) {
            case 'PENDING':
                return 'En attente';
            case 'PAID':
                return 'Payée';
            case 'REFUSED':
                return 'Refusée';
            case 'PARTIAL':
                return 'Partiellement payée';
            case 'DELAYED':
                return 'En retard';
            case 'REFUNDED':
                return 'Remboursée';
            case 'CANCELED':
                return 'Annulée';
            default:
                return 'Statut inconnu';
        }
    }
}
