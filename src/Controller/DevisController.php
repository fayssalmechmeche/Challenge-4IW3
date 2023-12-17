<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use App\Repository\FormulaRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function new(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository, FormulaRepository $formulaRepository, DevisRepository $devisRepository): Response
    {
        $devis = new Devis();
        $user = $this->getUser();
        $devis->setUser($this->getUser());
        $lastDevisNumber = $devisRepository->findLastDevisNumberForUser($user);
        $newDevisNumber = $this->generateNewDevisNumber($lastDevisNumber);

        $userEmail = $user ? $user->getEmail() : '';
        $products = $productRepository->findBy(['user' => $user]);
        $formulas = $formulaRepository->findBy(['user' => $user]);


        $form = $this->createForm(DevisType::class, $devis, [
            'user' => $user,
        ]);
        $form->get('devisNumber')->setData($newDevisNumber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $devis->setDevisNumber($newDevisNumber);
            $devisProductsJson = $request->request->get('devisProductsJson');
            if ($devisProductsJson) {
                $devisProductsData = json_decode($devisProductsJson, true);

                // Traiter chaque produit dans les données décodées
                foreach ($devisProductsData as $productData) {
                    $product = $productRepository->find($productData['product']);
                    if ($product) {
                        $devisProduct = new DevisProduct();
                        $devisProduct->setProduct($product);
                        $devisProduct->setQuantity($productData['quantity']);
                        $devis->addDevisProduct($devisProduct); // Supposant que vous avez une méthode addDevisProduct
                    }
                }
            }
            $entityManager->persist($devis);
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('devis/new.html.twig', [
            'devis' => $devis,
            'form' => $form->createView(),
            'userEmail' => $userEmail,
            'products' => $products,
            'formulas' => $formulas,
            'devisNumber' => $newDevisNumber,
        ]);
    }


    #[Route('/{id}/show', name: 'app_devis_show', methods: ['GET'])]
    public function show(Devis $devi): Response
    {
        $user = $this->getUser();
        $userEmail = $user ? $user->getEmail() : '';

        // Traitement des produits
        $productsCollection = $devi->getDevisProducts();
        $productsCollection->initialize();

        $productsArray = [];
        foreach ($productsCollection as $devisProduct) {
            $product = $devisProduct->getProduct();
            $productsArray[] = [
                'id' => $devisProduct->getId(),
                'name' => $product ? $product->getName() : '',
                'quantity' => $devisProduct->getQuantity(),
            ];
        }

        // Traitement des formules
        $formulasCollection = $devi->getDevisFormulas();
        $formulasCollection->initialize();

        $formulasArray = [];
        foreach ($formulasCollection as $devisFormula) {
            $formula = $devisFormula->getFormula();
            $formulasArray[] = [
                'id' => $devisFormula->getId(),
                'name' => $formula ? $formula->getName() : '',
                'quantity' => $devisFormula->getQuantity(),
            ];
        }

        return $this->render('devis/show.html.twig', [
            'devi' => $devi,
            'userEmail' => $userEmail,
            'products' => $productsArray,
            'formulas' => $formulasArray,
        ]);
    }


    #[Route('/product/{id}/price', name: 'api_product_price', methods: ['GET'])]
    public function getProductPrice($id, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }
        return new JsonResponse(['price' => $product->getPrice()]);
    }

    #[Route('/formula/{id}/price', name: 'api_formula_price', methods: ['GET'])]
    public function getFormulaPrice($id, FormulaRepository $formulaRepository): JsonResponse
    {
        $formula = $formulaRepository->find($id);
        if (!$formula) {
            return new JsonResponse(['error' => 'Formule non trouvée'], 404);
        }
        return new JsonResponse(['price' => $formula->getPrice()]);
    }

    #[Route('/{id}/edit', name: 'app_devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devi, EntityManagerInterface $entityManager, ProductRepository $productRepository, FormulaRepository $formulaRepository): Response
    {
        $user = $this->getUser();

        $userEmail = $user ? $user->getEmail() : '';

        $products = $productRepository->findBy(['user' => $user]);
        $formulas = $formulaRepository->findBy(['user' => $user]);

        $clientId = $devi->getCustomer()?->getId();

        // Convertir les DevisProducts en tableau
        $devisProductsArray = [];
        foreach ($devi->getDevisProducts() as $devisProduct) {
            $devisProductsArray[] = [
                'id' => $devisProduct->getProduct()->getId(),
                'name' => $devisProduct->getProduct()->getName(),
                'quantity' => $devisProduct->getQuantity(),
               'price' => $devisProduct->getProduct()->getPrice(),
                // Ajoutez d'autres champs si nécessaire
            ];
        }

        // Convertir les DevisFormulas en tableau
        $devisFormulasArray = [];
        foreach ($devi->getDevisFormulas() as $devisFormula) {
            $devisFormulasArray[] = [
                'id' => $devisFormula->getFormula()->getId(),
                'name' => $devisFormula->getFormula()->getName(),
                'quantity' => $devisFormula->getQuantity(),
                'price' => $devisFormula->getFormula()->getPrice(),
                // Ajoutez d'autres champs si nécessaire
            ];
        }

        // Regrouper les produits et les formules
        $devisItems = [
            'products' => $devisProductsArray,
            'formulas' => $devisFormulasArray,
        ];

        $form = $this->createForm(DevisType::class, $devi, [
            'user' => $user,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/edit.html.twig', [
            'devi' => $devi,
            'clientId' => $clientId,
            'userEmail' => $userEmail,
            'form' => $form->createView(),
            'products' => $products,
            'formulas' => $formulas,
            'devisItems' => $devisItems,
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

    private function generateNewDevisNumber(?string $lastDevisNumber): string
    {
        $year = date('Y');
        $month = date('m');
        $sequentialNumber = 1;

        if ($lastDevisNumber) {
            // Extrait le numéro séquentiel du dernier numéro de devis et l'incrémente
            $lastParts = explode('-', $lastDevisNumber);
            $lastSequential = (int) end($lastParts);
            $sequentialNumber = $lastSequential + 1;
        }

        return sprintf("%s-%s-%04d", $year, $month, $sequentialNumber);
    }

}
