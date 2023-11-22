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
        $csrfToken = $this->csrfTokenManager->getToken('delete_devis')->getValue();

        return $this->render('devis/index.html.twig', [
            'devis' => $devisRepository->findAll(),
            'csrf_token' => $csrfToken,
        ]);
    }

    #[Route('/api', name: 'api_devis_index', methods: ['GET'])]
    public function apiIndex(EntityManagerInterface $entityManager): Response
    {
        $devisRepository = $entityManager->getRepository(Devis::class);
        $devis = $devisRepository->findAll();

        $data = [];
        foreach ($devis as $devi) {
            $data[] = [
                'id' => $devi->getId(),
                'totalPrice' => $devi->getTotalPrice(),
                'totalDuePrice' => $devi->getTotalDuePrice(),
                'paymentStatus' => $devi->getPaymentStatus() ? $devi->getPaymentStatus()->value : '',
                'createdAt' => $devi->getCreatedAt() ? $devi->getCreatedAt()->format('Y-m-d ') : '',
                'customer' => $devi->getCustomer() ? $devi->getCustomer()->getName() : '', // Assurez-vous que getName() existe dans l'entité Customer
            ];
        }

        return $this->json($data);
    }

    #[Route('/new', name: 'app_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devis = new Devis();

        // Initialisation d'un objet DevisProduct
        $devisProduct = new DevisProduct();
        $devis->addDevisProduct($devisProduct);

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
