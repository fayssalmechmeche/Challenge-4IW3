<?php

namespace App\Controller\Admin;

use App\Repository\DevisRepository;
use App\Repository\SocietyRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminDashboardController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository, SocietyRepository $societyRepository, DevisRepository $devisRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'users' => $userRepository->findAll(),
            'societies' => $societyRepository->findAll(),
            'devis' => $devisRepository->findAll(),
        ]);
    }
}
