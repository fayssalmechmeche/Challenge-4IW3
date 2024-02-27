<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/api', name: 'api_category_index', methods: ['GET'])]
    public function apiIndex(CategoryRepository $categoryRepository): Response
    {
        $society = $this->getUser()->getSociety();

        if ($society) {
            $categories = $categoryRepository->findBy(['society' => $society]);
        } else {
            $categories = [];
        }

        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $society = $this->getUser()->getSociety();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Verification si le produit existe deja
            $categoryName = ucfirst($form->get('name')->getData());
            $isCategoryExist = $entityManager->getRepository(Category::class)->findBy(['name' => $categoryName, 'society' => $society]);

            if ($isCategoryExist) {
                $this->addFlash('error', 'Cette catégorie existe déjà.');
                return $this->redirectToRoute('app_category_new');
            }
            $category->setName($categoryName);
            $category->setSociety($society);
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'La nouvelle catégorie a été créé avec succès.');
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }elseif ($form->isSubmitted() && !$form->isValid()) {
            // Nouvelle condition pour gérer les soumissions de formulaire non valides
            $this->addFlash('error', 'Le formulaire contient des erreurs, veuillez vérifier vos informations.');
            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
            'form_action' => $this->generateUrl('app_category_new')
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été modifié avec succès.');
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }elseif ($form->isSubmitted() && !$form->isValid()) {
            // Nouvelle condition pour gérer les soumissions de formulaire non valides
            $this->addFlash('error', 'Le formulaire contient des erreurs, veuillez vérifier vos informations.');
            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
            'form_action' => $this->generateUrl('app_category_edit', ['id' => $category->getId()])
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        // Vérifiez si le catégorie est utilisé dans un produit
        if (!$category->getProducts()->isEmpty()) {
            $this->addFlash('error', 'Cette catégorie est utilisée dans un produit. Supprimez d\'abord le produit lié.');
            return $this->redirectToRoute('app_category_index');
        }
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success', 'La catégorie a bien été supprimé.');
        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
