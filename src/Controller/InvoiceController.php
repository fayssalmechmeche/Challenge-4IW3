<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\DevisRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use DateTime;


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
                'devisNumber' => $devi -> getDevisNumber(),
                'createdAt' => $devi->getCreatedAt() ? $devi->getCreatedAt()->format('Y-m-d') : '',
                'customer' => $customerName,
                'depositStatus' => $devi->getDepositStatus() ? $devi->getDepositStatus()->value : 'NON_EXISTANT',
            ];
        }

        return $this->json($data);
    }

    #[Route('/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, InvoiceRepository $invoiceRepository,DevisRepository $devisRepository): Response
    {
        $devisId = $request->query->get('devisId');
        $invoice = new Invoice();
        $user = $this->getUser();
        $lastInvoiceNumber = $invoiceRepository->findLastInvoiceNumberForUser($user);
        $newInvoiceNumber = $this->generateNewinvoiceNumber($lastInvoiceNumber);
        $invoice->setUser($user);
        $user = $this->getUser();
        $userEmail = $user ? $user->getEmail() : '';

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);
        $devis = $devisRepository->find($devisId);


        $customer = $devis->getCustomer();
        return $this->render('invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
            'userEmail' => $userEmail,
            'invoiceNumber' => $newInvoiceNumber,
            'customer' => $customer,
            'devis' => $devis,
        ]);
    }

    #[Route('/new/ajax', name: 'app_invoice_new_ajax', methods: ['POST'])]
    public function newAjax(Request $request, EntityManagerInterface $entityManager, InvoiceRepository $invoiceRepository, DevisRepository $devisRepository, LoggerInterface $logger): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $devis = $devisRepository->find($data['devisId']);
        $invoice = new Invoice();
        $invoice->setUser($user);
        $invoice->setDevis($devis);
        $invoice->setInvoiceNumber($data['invoiceNumber']);
        $invoice->setTaxe($data['taxe']);
        $invoice->setTotalPrice($data['totalPrice']);
        $invoice->setTotalDuePrice($data['totalDuePrice']);
        $invoice->setRemise($data['remise']);
        $invoice->setPaymentStatus($data['paymentStatus']);
        $paymentDueTime = new DateTime($data['paymentDueTime']);
        $invoice->setPaymentDueTime($paymentDueTime);
        $createdAt = new DateTime();
        $invoice->setCreatedAt($createdAt);
        $entityManager->persist($invoice);
        $entityManager->flush();
        return new JsonResponse(['success' => true]);
    }



    #[Route('/devisDetail', name: 'invoice_devis_detail', methods: ['GET','POST'])]
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
        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $request->request->get('_token'))) {
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
