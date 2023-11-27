<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/customer')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'app_customer_index', methods: ['GET', 'POST'])]
    public function index(CustomerRepository $customerRepository): Response
    {
        return $this->render('customer/index.html.twig', [
            'customers' => $customerRepository->findAll(),
        ]);
    }

    #[Route('/api', name: 'api_customer_index', methods: ['GET'])]
    public function apiIndex(EntityManagerInterface $entityManager): Response
    {
        $productRepository = $entityManager->getRepository(Customer::class);
        $customers = $productRepository->findAll();
        $data = [];
        foreach ($customers as $customer) {
            $data[] = [
                'id' => $customer->getId(),
                'name' => $customer->getName(),
                'lastName' => $customer->getLastName(),
                'nameSociety' => $customer->getNameSociety(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/api/{id}', name: 'api_customer_details', methods: ['GET'])]
    public function apiCustomerDetails(Customer $customer): Response
    {
        $devisCounts = [
            'pending' => 0,
            'paid' => 0,
            'partial' => 0,
            'refunded' => 0
        ];

        foreach ($customer->getDevis() as $devis) {
            // Incrémenter le compteur en fonction du statut de paiement
            $status = strtolower($devis->getPaymentStatus()->value);
            if (array_key_exists($status, $devisCounts)) {
                $devisCounts[$status]++;
            }
        }

        $totalDevisCount = count($customer->getDevis());

        return $this->json([
            'id' => $customer->getId(),
            'name' => $customer->getName(),
            'lastName' => $customer->getLastName(),
            'nameSociety' => $customer->getNameSociety(),
            'streetName' => $customer->getStreetName(),
            'streetNumber' => $customer->getStreetNumber(),
            'city' => $customer->getCity(),
            'postalCode' => $customer->getPostalCode(),
            'devisCounts' => $devisCounts,
            'totalDevisCount' => $totalDevisCount
        ]);
    }

    #[Route('/new', name: 'app_customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($customer);
            $entityManager->flush();

            // Ajouter un message flash ICI si le formulaire est soumis et valide
            $this->addFlash('success', 'Le nouveau client a été créé avec succès.');

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'form_action' => $this->generateUrl('app_customer_new')
        ]);
    }



    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(Customer $customer): Response
    {
        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'form_action' => $this->generateUrl('app_customer_edit', ['id' => $customer->getId()])
        ]);
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['POST'])]
    public function delete(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_customer', $request->request->get('_token'))) {
            // Vérifiez si le client est utilisé dans un devis
            if (!$customer->getDevis()->isEmpty()) {
                $this->addFlash('error', 'Ce client est utilisé dans un devis. Supprimez d\'abord le devis lié.');
                return $this->redirectToRoute('app_customer_index');
            }

            $entityManager->remove($customer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
    }


}
