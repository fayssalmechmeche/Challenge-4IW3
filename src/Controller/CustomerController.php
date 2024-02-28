<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/customer')]
#[IsGranted('ROLE_SOCIETY')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'app_customer_index', methods: ['GET', 'POST'])]
    public function index(CustomerRepository $customerRepository): Response
    {
        $society = $this->getSociety();
        $customers = $customerRepository->findBy(['society' => $society]);
        return $this->render('customer/index.html.twig', [
            'customers' => $customers,
        ]);
    }

    #[Route('/api', name: 'api_customer_index', methods: ['GET'])]
    public function apiIndex(CustomerRepository $customerRepository): Response
    {
        $society = $this->getSociety();

        if ($society) {
            $customers = $customerRepository->findBy(['society' => $society]);
        } else {
            $customers = [];
        }

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
        $society = $this->getSociety();
        if ($society->getId() != $customer->getSociety()->getId()) {
            return $this->redirectToRoute('app_customer_index');
        }

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
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhoneNumber(),
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
            $this->extracted($customer);
            $customer->setSociety($this->getSociety());
            $entityManager->persist($customer);
            $entityManager->flush();

            $this->addFlash('success', 'Le nouveau client a été créé avec succès.');
            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            // Nouvelle condition pour gérer les soumissions de formulaire non valides
            $this->addFlash('error', 'Le formulaire contient des erreurs, veuillez vérifier vos informations.');
            return $this->redirectToRoute('app_customer_index');
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
        $society = $this->getSociety();
        if ($society->getId() != $customer->getSociety()->getId()) {
            return $this->redirectToRoute('app_customer_index');
        }

        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        $society = $this->getSociety();
        if ($society->getId() != $customer->getSociety()->getId()) {
            return $this->redirectToRoute('app_customer_index');
        }

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->extracted($customer);
            $entityManager->flush();

            $this->addFlash('success', 'Le client a été modifié avec succès.');
            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }elseif ($form->isSubmitted() && !$form->isValid()) {

            $this->addFlash('error', 'Le formulaire contient des erreurs, veuillez vérifier vos informations.');
            return $this->redirectToRoute('app_customer_index');
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
        $society = $this->getSociety();
        if ($society->getId() != $customer->getSociety()->getId()) {
            return $this->redirectToRoute('app_customer_index');
        }
        
        if ($this->isCsrfTokenValid('delete_customer', $request->request->get('_token'))) {
            // Vérifiez si le client est utilisé dans un devis
            if (!$customer->getDevis()->isEmpty()) {
                $this->addFlash('error', 'Ce client est utilisé dans un devis. Supprimez d\'abord le devis lié.');
                return $this->redirectToRoute('app_customer_index');
            }

            $entityManager->remove($customer);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Le client a bien été supprimé.');
        return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
    }

    private function capitalizeFirstLetter(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return ucfirst(strtolower($value));
    }

    /**
     * @param Customer $customer
     * @return void
     */
    public function extracted(Customer $customer): void
    {
        $customer->setName($this->capitalizeFirstLetter($customer->getName()));
        $customer->setLastName($this->capitalizeFirstLetter($customer->getLastName()));
        $customer->setNameSociety($this->capitalizeFirstLetter($customer->getNameSociety()));
        $customer->setStreetName($this->capitalizeFirstLetter($customer->getStreetName()));
        $customer->setCity($this->capitalizeFirstLetter($customer->getCity()));
        $customer->setEmail(strtolower($customer->getEmail()));
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
}
