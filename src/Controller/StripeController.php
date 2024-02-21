<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceStatus;
use App\Entity\InvoiceType;
use Stripe\Stripe;
use Stripe\Webhook;
use Psr\Log\LoggerInterface;
use App\Service\Stripe\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class StripeController extends AbstractController
{
    #[Route('/checkout/{token}', name: 'checkout_index')]
    public function checkout(
        string $token,
        StripeService $stripeService,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        // on verifie si le client veut payer un acompte ou le solde
        $isDeposit = $request->query->get('deposit');

        // on verifie si la facture existe en bdd
        /** @var Invoice $invoice */
        $invoice = $entityManager->getRepository(Invoice::class)->findOneBy(['token' => $token, 'user' => $this->getUser()]);
        if (!$invoice) {
            return $this->redirectToRoute('app_invoice_index');
        }

        // on verifie si la commande a déjà été payée en bdd
        if ($invoice->getPaymentStatus() && $invoice->getPaymentStatus() != InvoiceStatus::Pending) {
            // si oui on redirige vers la page de succes
            $this->addFlash('success', 'Votre facture a déjà été payée');
            return $this->redirectToRoute('checkout_success', ['token' => $invoice->getToken()]);
        }

        // le scenario suiavnt sert a verifier si la session existe en bdd et si elle est expirée
        // on verifie si la session existe en bdd
        if ($invoice->getStripeSessionId()) {
            // si oui on la recupere
            $session = $stripeService->retriveSession($invoice->getStripeSessionId());
            // on verifie si la session a été payée chez stripe
            if ($session && $session->payment_status == "paid") {
                // si oui on redirige vers la page de succes
                // if status is paid, we update the invoice status
                $invoice->setPaymentStatus(InvoiceStatus::Paid);
                $entityManager->flush();
                $this->addFlash('success', 'Votre facture a déjà été payée');
                return $this->redirectToRoute('checkout_success', ['token' => $invoice->getToken()]);
            }
            if ($session->status != "expired") {
                $stripeService->cancelSession($session->id);
            }
            $session = $stripeService->createPaymentIntent($invoice, $isDeposit);
            $invoice->setStripeSessionId($session->id);
        } else {
            // si non (pas de session en bdd) on en crée une nouvelle
            $session = $stripeService->createPaymentIntent($invoice, $isDeposit);
            $invoice->setStripeSessionId($session->id);
        }
        $invoice->setTotalDuePrice($invoice->getTotalDuePrice() - $session->amount_total / 100);
        $invoice->setInvoiceType($isDeposit ? InvoiceType::Deposit : InvoiceType::Invoice);
        // on enregistre l'id de la session en bdd
        $entityManager->flush();
        //on redirige vers la page de paiement
        return $this->redirect($session->url);
    }

    #[Route('/checkout/success/{token}', name: 'checkout_success')]
    public function success(string $token, EntityManagerInterface $entityManager): Response
    {
        $invoice = $entityManager->getRepository(Invoice::class)->findOneBy(['token' => $token]);

        if (!$invoice) {
            return $this->redirectToRoute('default_index');
        }

        return $this->render('checkout/success.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/checkout/cancel/{token}', name: 'checkout_cancel')]
    public function cancel(string $token, EntityManagerInterface $entityManager): Response
    {
        $invoice = $entityManager->getRepository(Invoice::class)->findOneBy(array('token' => $token));
        if ($invoice->getPaymentStatus() == InvoiceStatus::Paid || $invoice->getPaymentStatus() == InvoiceStatus::Partial) {
            $this->addFlash('success', 'La facture a déjà été payée ou partiellement payée');
            return $this->redirectToRoute('default_index', ['token' => $invoice->getToken()]);
        }

        return $this->render('checkout/cancel.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/checkout/complete/{token}', name: 'checkout_partial_complete')]
    public function complete(Invoice $invoice, string $token, EntityManagerInterface $entityManager): Response
    {
        if (!$invoice) {
            return $this->redirectToRoute('default_index');
        }
        if ($invoice->getPaymentStatus() == InvoiceStatus::Paid) {
            $this->addFlash('success', 'La facture a déjà été payée');
            return $this->redirectToRoute('checkout_success', ['token' => $invoice->getToken()]);
        }
        $invoice->setPaymentStatus(InvoiceStatus::Paid);
        $entityManager->flush();
        $this->addFlash('success', 'La facture a bien été payée');

        return $this->redirectToRoute('checkout_success', ['token' => $token]);
    }


    #[Route('/stripe_webhooks', name: 'app_stripe')]
    public function testWebhook(
        Request $request,
        LoggerInterface $logger,
        StripeService $stripeService,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            Stripe::setApiKey($this->getParameter('stripe_secret_key'));
            $webhookSecret = $this->getParameter('stripe_webhook_secret');
            $event = $request->query;
            // Parse the message body and check the signature
            $signature = $request->headers->get('stripe-signature');

            if ($webhookSecret) {
                try {
                    $event = Webhook::constructEvent(
                        $request->getcontent(),
                        $signature,
                        $webhookSecret
                    );
                } catch (\Exception $e) {
                    return new JsonResponse([['error' => $e->getMessage(), 'status' => 403]]);
                }
            } else {
                $request->query;
            }

            $type = $event['type'];
            $object = $event['data']['object'];
            $today = date("Y-m-d", strtotime('today'));
            switch ($type) {
                case 'checkout.session.completed':
                    /** @var Session $object */
                    /** @var Invoice $invoice */
                    $invoice = $entityManager->getRepository(Invoice::class)->findOneBy(['stripeSessionId' => $object->id]);
                    if (!$invoice) {
                        $logger->error('Invoice not found');
                        return new JsonResponse([['error' => 'Invoice not found', 'status' => 403]]);
                    }
                    if ($event['data']['object']['metadata']['type'] == 'invoice') {
                        $invoice->setPaymentStatus(InvoiceStatus::Paid);
                    } else {
                        $paymentDue = $invoice->getTotalDuePrice() - floatval($event['data']['object']['metadata']['price']);
                        $invoice->setTotalDuePrice($paymentDue);
                        $invoice->setPaymentStatus(InvoiceStatus::Partial);
                    }
                    $entityManager->flush();
                    break;
            }
            return new JsonResponse([['status' => 200]]);
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
            return new JsonResponse([['error' => $e->getMessage(), 'status' => 403]]);
        }
    }
}