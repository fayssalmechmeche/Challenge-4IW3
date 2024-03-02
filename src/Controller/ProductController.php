<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Formula;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



#[Route('/product')]
#[IsGranted('ROLE_SOCIETY')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $society = $this->getSociety();

        if ($society) {
            $products = $productRepository->findBy(['society' => $society]);
        } else {
            $products = [];
        }
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/api', name: 'api_product_index', methods: ['GET'])]
    public function apiIndex(ProductRepository $productRepository): Response
    {
        $society = $this->getSociety();

        if ($society) {
            $products = $productRepository->findBy(['society' => $society]);
        } else {
            $products = [];
        }

        $data = [];
        foreach ($products as $product) {
            $categoryName = $product->getCategory() ? $product->getCategory()->getName() : 'Aucune';
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'category' => $categoryName,
            ];
        }

        return $this->json($data);
    }


    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $society = $this->getSociety();

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, ['society' => $society]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productName = ucfirst($form->get('name')->getData());
            $product->setName($productName);
            $product->setSociety($this->getSociety());
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Le nouveau produit a été créé avec succès.');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }elseif ($form->isSubmitted() && !$form->isValid()) {
            // Nouvelle condition pour gérer les soumissions de formulaire non valides
            $this->addFlash('error', 'Le formulaire contient des erreurs, veuillez vérifier vos informations.');
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'form_action' => $this->generateUrl('app_product_new')
        ]);
    }


    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        $society = $this->getSociety();
        if ($society->getId() != $product->getSociety()->getId()) {
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $society = $this->getSociety();
        if ($society->getId() != $product->getSociety()->getId()) {
            return $this->redirectToRoute('app_product_index');
        }

        $form = $this->createForm(ProductType::class, $product, ['society' => $society]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();
            $this->addFlash('success', 'Le produit a été modifié avec succès.');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }elseif ($form->isSubmitted() && !$form->isValid()) {
            // Nouvelle condition pour gérer les soumissions de formulaire non valides
            $this->addFlash('error', 'Le formulaire contient des erreurs, veuillez vérifier vos informations.');
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'form_action' => $this->generateUrl('app_product_edit', ['id' => $product->getId()])
        ]);
    }


    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $society = $this->getSociety();
        if ($society->getId() != $product->getSociety()->getId()) {
            return $this->redirectToRoute('app_product_index');
        }

            // Vérifiez si le produit est utilisé dans une formule
            if (!$product->getProductFormulas()->isEmpty()) {
                $this->addFlash('error', 'Ce produit est utilisé dans une formule. Supprimez d\'abord la formule liée.');
                return $this->redirectToRoute('app_product_index');
            }
            if (!$product->getDevisProducts()->isEmpty()) {
                $this->addFlash('error', 'Ce produit est utilisé dans un devis. Supprimez d\'abord le devis lié.');
                return $this->redirectToRoute('app_product_index');
            }

            $entityManager->remove($product);
            $entityManager->flush();

        $this->addFlash('success', 'Le produit a bien été supprimé.');
        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
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
