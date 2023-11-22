<?php

namespace App\Controller;

use App\Entity\Formula;
use App\Form\FormulaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\ProductFormula;

#[Route('/formula')]
class FormulaController extends AbstractController
{
    #[Route('/', name: 'app_formula_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $formulas = $entityManager
            ->getRepository(Formula::class)
            ->findAll();

        return $this->render('formula/index.html.twig', [
            'formulas' => $formulas,
        ]);
    }

    #[Route('/api', name: 'api_formula_index', methods: ['GET'])]
    public function apiIndex(EntityManagerInterface $entityManager): Response
    {
        $formulaRepository = $entityManager->getRepository(Formula::class);
        $formulas = $formulaRepository->findAll();

        $data = [];
        foreach ($formulas as $formula) {
            $data[] = [
                'id' => $formula->getId(),
                'name' => $formula->getName(),
                'image' => $formula->getPicture() ? '/images/formulas/'.$formula->getPicture() : null,
                'price' => $formula->getPrice(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/new', name: 'app_formula_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formula = new Formula();

        $form = $this->createForm(FormulaType::class, $formula);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de l'image
            $file = $form->get('picture')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('formulas_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal
                }

                $formula->setPicture($newFilename);
            }

            $entityManager->persist($formula);
            $entityManager->flush();

            return $this->redirectToRoute('app_formula_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formula/new.html.twig', [
            'formula' => $formula,
            'form' => $form->createView(),
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
        $form = $this->createForm(FormulaType::class, $formula);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formula_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formula/edit.html.twig', [
            'formula' => $formula,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formula_delete', methods: ['POST'])]
    public function delete(Request $request, Formula $formula, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_formula', $request->request->get('_token'))) {
            $imageName = $formula->getPicture();
            if ($imageName) {
                $imagePath = $this->getParameter('formulas_images_directory') . '/' . $imageName;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $entityManager->remove($formula);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_formula_index', [], Response::HTTP_SEE_OTHER);
    }

}
