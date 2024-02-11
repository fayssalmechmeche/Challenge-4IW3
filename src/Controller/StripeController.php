<?php

namespace App\Controller;

use App\Entity\Invoice;
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
            return $this->redirectToRoute('booking_index');
        }

        // on verifie si la commande a déjà été payée en bdd
        if ($invoice->getPaymentStatus() && $invoice->getPaymentStatus() != PaymentStatus::Pending) {
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
                $invoice->setPaymentStatus(PaymentStatus::Paid);
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
        $invoice->setPaymentType($isDeposit ? PaymentType::Deposit : PaymentType::Full);
        // on enregistre l'id de la session en bdd
        $entityManager->flush();
        //on redirige vers la page de paiement
        return $this->redirect($session->url);
    }





    #[Route('/stripe_webhooks', name: 'app_stripe')]
    public function testWebhook(
        Request $request,
        LoggerInterface $logger,
        StripeService $stripeService,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            Stripe::setApiKey($this->getParameter('stripe_secret_key'));
            $webhookSecret = $this->getParameter('stripe_secret_webhook');
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
                    $invoice = $stripeService->retriveInvoice($object['invoice']);
                    $charge = $stripeService->retriveCharge($invoice->charge);

                    $entityManager->flush();
            }
            return new JsonResponse([['status' => 200]]);
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
            return new JsonResponse([['error' => $e->getMessage(), 'status' => 403]]);
        }
    }
}
