<?php

namespace App\Controller;

use DateTime;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use Psr\Log\LoggerInterface;
use App\Entity\InvoiceStatus;
use App\Entity\PaymentStatus;
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

#[Route('/invoice')]
class InvoiceController extends AbstractController
{
    #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoiceRepository->findAll(),
        ]);
    }

    #[Route('/api', name: 'api_invoice_devis_index', methods: ['GET'])]
    public function apiIndex(DevisRepository $devisRepository): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        if ($user) {
            $devis = $devisRepository->findPendingByUser($user);
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

    #[Route('/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, InvoiceRepository $invoiceRepository, DevisRepository $devisRepository, MailjetService $mailjetService): Response
    {
        $devisId = $request->query->get('devisId');
        $deposit = $request->query->get('deposit');
        $invoice = new Invoice();
        $user = $this->getUser();
        $lastInvoiceNumber = $invoiceRepository->findLastInvoiceNumberForUser($user);
        $newInvoiceNumber = $this->generateNewinvoiceNumber($lastInvoiceNumber);
        $invoice->setUser($user);
        $devis = $devisRepository->find($devisId);
        $invoice->setDevis($devis);
        $user = $this->getUser();
        $userEmail = $user ? $user->getEmail() : '';

        $taxeValue = ($devis->getTotalPrice() * $devis->getTaxe()) / 100;
        $totalTTC = $devis->getTotalPrice() + $taxeValue;
        $depositAmount = ($devis->getTotalPrice() * ($devis->getDepositPercentage() / 100)) + ($taxeValue * ($devis->getDepositPercentage() / 100));
        $new  = $totalTTC;

        if ($request->isMethod('POST')) {

            $invoice->setInvoiceNumber($newInvoiceNumber);
            $invoice->setTaxe($taxeValue);
            $invoice->setTotalPrice(round($new));
            $invoice->setTotalDuePrice(round($new));
            $invoice->setRemise(0);
            $invoice->setPaymentStatus(InvoiceStatus::Pending);
            $invoice->setToken(uniqid());
            $paymentDueTime = new DateTime('now + 10 days');
            $invoice->setPaymentDueTime($paymentDueTime);
            $createdAt = new DateTime();
            $invoice->setCreatedAt($createdAt);
            $entityManager->persist($invoice);
            $entityManager->flush();

            $mailjetService->sendEmail(
                $invoice->getDevis()->getCustomer()->getEmail(),
                'Nouvelle facture créée',
                MailjetService::TEMPLATE_INVOICE_NO_DEPOSIT,
                [
                    'firstName' => $invoice->getDevis()->getCustomer()->getName(),
                    'name' => $invoice->getDevis()->getCustomer()->getLastName(),
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
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

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
            $lastSequential = (int) end($lastParts);
            $sequentialNumber = $lastSequential + 1;
        }

        return sprintf("%s-%s-%04d", $year, $month, $sequentialNumber);
    }
}
