<?php

namespace App\Controller;

use App\Entity\Formula;
use App\Form\FormulaType;
use App\Entity\ProductFormula;
use App\Repository\FormulaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/formula')]
#[IsGranted('ROLE_SOCIETY')]
class FormulaController extends AbstractController
{
    #[Route('/', name: 'app_formula_index', methods: ['GET'])]
    public function index(FormulaRepository $formulaRepository): Response
    {
        $society = $this->getSociety();

        if ($society) {
            $formulas = $formulaRepository->findBy(['society' => $society]);
        } else {
            $formulas = [];
        }
        return $this->render('formula/index.html.twig', [
            'formulas' => $formulas,
        ]);
    }

    #[Route('/api', name: 'api_formula_index', methods: ['GET'])]
    public function apiIndex(FormulaRepository $formulaRepository): Response
    {
        $society = $this->getSociety();

        if ($society) {
            $formulas = $formulaRepository->findBy(['society' => $society]);
        } else {
            $formulas = [];
        }

        $data = [];
        foreach ($formulas as $formula) {
            $data[] = [
                'id' => $formula->getId(),
                'name' => $formula->getName(),
                'price' => $formula->getPrice(),
            ];
        }

        return $this->json($data);
    }


    #[Route('/api/{id}', name: 'api_formula_details', methods: ['GET'])]
    public function apiFormulaDetails(EntityManagerInterface $entityManager, $id): Response
    {
        $formulaRepository = $entityManager->getRepository(Formula::class);
        $formula = $formulaRepository->find($id);
        $productsData = [];
        foreach ($formula->getProductFormulas() as $productFormula) {
            $product = $productFormula->getProduct();
            $productsData[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'quantity' => $productFormula->getQuantity()

            ];
        }
        $data = [
            'id' => $formula->getId(),
            'name' => $formula->getName(),
            'price' => $formula->getPrice(),
            'products' => $productsData
        ];

        return $this->render('formula/show.html.twig', [
            'data' => $data
        ]);
    }

    #[Route('/api/modify/{id}', name: 'api_modify_formula_details', methods: ['GET'])]
    public function apiModifyFormulaDetails(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $formulaRepository = $entityManager->getRepository(Formula::class);
        $formula = $formulaRepository->find($id);

        if (!$formula) {
            return new JsonResponse(['error' => 'Formula not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $productsData = [];
        foreach ($formula->getProductFormulas() as $productFormula) {
            $product = $productFormula->getProduct();
            $productsData[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'quantity' => $productFormula->getQuantity(),
                'price' => $product->getPrice(),
            ];
        }

        $data = [
            'id' => $formula->getId(),
            'name' => $formula->getName(),
            'products' => $productsData
        ];
        return new JsonResponse($data);
    }

    #[Route('/new', name: 'app_formula_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formula = new Formula();
        $society = $this->getSociety();
        $form = $this->createForm(FormulaType::class, $formula, [
            'society' => $society,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formulaName = ucfirst($form->get('name')->getData());
            $formula->setName($formulaName);
            $society = $this->getSociety();
            $formula->setSociety($society);

            $entityManager->persist($formula);
            $entityManager->flush();
            $this->addFlash('success', 'La création de la formule a été effectuée avec succès.');
            return $this->redirectToRoute('app_formula_index', [], Response::HTTP_SEE_OTHER);
        }elseif ($form->isSubmitted() && !$form->isValid()) {
            // Nouvelle condition pour gérer les soumissions de formulaire non valides
            $this->addFlash('error', 'Le formulaire contient des erreurs, veuillez vérifier vos informations.');
            return $this->redirectToRoute('app_formula_index');
        }

        return $this->render('formula/new.html.twig', [
            'formula' => $formula,
            'form' => $form->createView(),
            'form_action' => $this->generateUrl('app_formula_new')
        ]);
    }



    #[Route('/{id}', name: 'app_formula_show', methods: ['GET'])]
    public function show(Formula $formula): Response
    {
        return $this->render('formula/show.html.twig', [
            'formula' => $formula,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formula_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formula $formula, EntityManagerInterface $entityManager): Response
    {
        $society = $this->getSociety();
        $form = $this->createForm(FormulaType::class, $formula, [
            'society' => $society,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formulaName = ucfirst($form->get('name')->getData());
            $formula->setName($formulaName);
            $entityManager->flush();

            return $this->redirectToRoute('app_formula_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formula/edit.html.twig', [
            'formula' => $formula,
            'form' => $form,
            'form_action' => $this->generateUrl('app_formula_edit', ['id' => $formula->getId()])
        ]);
    }

    #[Route('/{id}', name: 'app_formula_delete', methods: ['POST'])]
    public function delete(Request $request, Formula $formula, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_formula', $request->request->get('_token'))) {
            $entityManager->remove($formula);
            $entityManager->flush();
        }
        $this->addFlash('success', 'La formule a bien été supprimé.');
        return $this->redirectToRoute('app_formula_index', [], Response::HTTP_SEE_OTHER);
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
