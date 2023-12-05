<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\DevisProduct;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/devis')]
class DevisController extends AbstractController
{
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[Route('/', name: 'app_devis_index', methods: ['GET'])]
    public function index(DevisRepository $devisRepository): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        if ($user) {
            $devis = $devisRepository->findBy(['user' => $user]);
        } else {
            $devis = []; // Si aucun utilisateur n'est connecté, aucun devis n'est retourné
        }

        $csrfToken = $this->csrfTokenManager->getToken('delete_devis')->getValue();

        return $this->render('devis/index.html.twig', [
            'devis' => $devis,
            'csrf_token' => $csrfToken,
        ]);
    }

    #[Route('/api', name: 'api_devis_index', methods: ['GET'])]
    public function apiIndex(DevisRepository $devisRepository): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        if ($user) {
            $devis = $devisRepository->findBy(['user' => $user]);
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
                'totalPrice' => $devi->getTotalPrice(),
                'totalDuePrice' => $devi->getTotalDuePrice(),
                'paymentStatus' => $devi->getPaymentStatus() ? $devi->getPaymentStatus()->value : '',
                'createdAt' => $devi->getCreatedAt() ? $devi->getCreatedAt()->format('Y-m-d') : '',
                'customer' => $customerName,
            ];
        }

        return $this->json($data);
    }


    #[Route('/new', name: 'app_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devis = new Devis();
        $devis->setUser($this->getUser()); // Associez le devis à l'utilisateur connecté

        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($devis);
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/new.html.twig', [
            'devis' => $devis,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_devis_show', methods: ['GET'])]
    public function show(Devis $devi): Response
    {
        return $this->render('devis/show.html.twig', [
            'devi' => $devi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        // Pas besoin d'initialiser un nouveau DevisProduct ici, car on édite un devis existant
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/edit.html.twig', [
            'devi' => $devi,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_devis_delete', methods: ['POST'])]
    public function delete(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_devis', $request->request->get('_token'))) {
            $entityManager->remove($devi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
    }

}
