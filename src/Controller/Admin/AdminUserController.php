<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/user', name: 'admin_user_')]
class AdminUserController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $users = $this->entityManagerInterface->getRepository(User::class)->findAll(),
        ]);
    }


    #[Route('/new', name: 'new')]
    public function new(Request $request)
    {
        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->getUser()->getSociety()->getName() . uniqid());
            $user->setCreatedAt(new \DateTime());
            $user->setSociety($this->getUser()->getSociety());
            $user->setVerified(false);
            $request->get("roles");
            if (in_array('ROLE_ADMIN', $form->get('roles')->getData())) {
                $this->addFlash('danger', 'Vous ne pouvez pas attribuer le rôle administrateur');
                return $this->redirectToRoute('admin_user_index');
            }


            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
            $this->addFlash('success', 'Utilisateur ajouté avec succès');
        }
        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/show/{id}', name: 'show')]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(User $user, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (in_array('ROLE_ADMIN', $form->get('Roles')->getData())) {
                $this->addFlash('danger', 'Vous ne pouvez pas attribuer le rôle administrateur');
                return $this->redirectToRoute('admin_user_index');
            }
            $this->entityManagerInterface->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('admin_user_index');
        }
    }

    #[Route('/delete/{id}/{token}', name: 'delete')]
    public function delete(User $user, string $token): Response
    {
        if ($this->isCsrfTokenValid('delete-users' . $user->getId(), $token)) {
            $this->entityManagerInterface->remove($user);
            $this->entityManagerInterface->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès');
            return $this->redirectToRoute('admin_user_index');
        }
        $this->addFlash('danger', 'Erreur lors de la suppression');
        return $this->redirectToRoute('admin_user_index');
    }
}