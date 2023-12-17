<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;



#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $user = $this->getUser();

        if ($user) {
            $products = $productRepository->findBy(['user' => $user]);
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
        $user = $this->getUser();

        if ($user) {
            $products = $productRepository->findBy(['user' => $user]);
        } else {
            $products = [];
        }

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'productCategory' => $product->getProductCategory(),
            ];
        }

        return $this->json($data);
    }


    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productName = ucfirst($form->get('name')->getData());
            $product->setName($productName);
            $product->setUser($this->getUser());
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
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
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_product', $request->request->get('_token'))) {
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
        }
        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

}
