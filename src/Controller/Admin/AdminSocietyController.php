<?php

namespace App\Controller\Admin;

use App\Entity\Society;
use App\Form\Admin\SocietyType;
use App\Repository\UserRepository;
use App\Repository\DevisRepository;
use App\Repository\SocietyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/society', name: 'admin_society_')]
class AdminSocietyController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/society/index.html.twig', [
            'societies' => $societies = $this->entityManagerInterface->getRepository(Society::class)->findAll(),
        ]);
    }

    #[Route('/api', name: 'api_society_index', methods: ['GET'])]
    public function apiIndex(EntityManagerInterface $entityManager, CsrfTokenManagerInterface $tokenManager): Response
    {
        $societyRepository = $entityManager->getRepository(Society::class);
        $societies = $societyRepository->findAll();
        $data = [];
        foreach ($societies as $society) {
            $data[] = [
                'id' => $society->getId(),
                'name' => $society->getName(),
                'address' => $society->getAddress(),
                'phone' => $society->getPhone(),
                'email' => $society->getEmail(),
                'token' => $tokenManager->getToken('delete-society' . $society->getId())->getValue(),
            ];
        }
        return $this->json($data);
    }



    #[Route('/new', name: 'new')]
    public function new(Request $request)
    {
        $society = new Society();
        $form = $this->createForm(SocietyType::class, $society);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManagerInterface->persist($society);
            $this->entityManagerInterface->flush();
            $this->addFlash('success', 'Société créée avec succès');
            return $this->redirectToRoute('admin_society_index');
        }
        return $this->render('admin/society/new.html.twig', [
            'society' => $society,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'show')]
    public function show(Society $society): Response
    {
        return $this->render('admin/society/show.html.twig', [
            'society' => $society,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Society $society, Request $request): Response
    {
        $form = $this->createForm(SocietyType::class, $society);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManagerInterface->flush();
            $this->addFlash('success', 'Société modifiée avec succès');
            return $this->redirectToRoute('admin_society_index');
        }
        return $this->render('admin/society/edit.html.twig', [
            'society' => $society,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}/{token}', name: 'delete')]
    public function delete(Society $society, string $token): Response
    {


        if (!$this->isCsrfTokenValid('delete-society' . $society->getId(), $token)) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('admin_society_index');
        }
        $this->entityManagerInterface->remove($society);
        $this->entityManagerInterface->flush();
        $this->addFlash('success', 'Société supprimée avec succès');
        return $this->redirectToRoute('admin_society_index');
    }
}
