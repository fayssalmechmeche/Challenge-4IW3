<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Society;
use const App\Entity\ROLE_ADMIN;
use App\Form\Admin\AdminUserType;
use App\Repository\UserRepository;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
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

    #[Route('/api', name: 'api_user_index', methods: ['GET'])]
    public function apiIndex(EntityManagerInterface $entityManager, CsrfTokenManagerInterface $tokenManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();
        $data = [];
        foreach ($users as $user) {
            if (in_array(ROLE_ADMIN, $user->getRoles())) {
                continue;
            }
            $data[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'status' => $user->isVerified(),
                'society' => $user->getSociety()->getName(),
                'token' => $tokenManager->getToken('delete-users' . $user->getId())->getValue(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/api/{id}', name: 'api_user_society_index', methods: ['GET'])]
    public function apiSocietyShow(UserRepository $userRepository, CsrfTokenManagerInterface $tokenManager, Society $society): Response
    {
        $users = $userRepository->findBy(['society' => $society]);
        $data = [];
        foreach ($users as $user) {
            if (in_array(ROLE_ADMIN, $user->getRoles())) {
                continue;
            }
            $data[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'status' => $user->isVerified(),
                'society' => $user->getSociety()->getName(),
                'token' => $tokenManager->getToken('delete-users' . $user->getId())->getValue(),
            ];
        }
        return $this->json($data);
    }


    #[Route('/new', name: 'new')]
    public function new(Request $request, UserRepository $userRepository)
    {
        if ($request->isXmlHttpRequest()) {
            // if ($this->isCsrfTokenValid('delete-users' . $user->getId(), $token)) {
            // }
            $content = $request->getContent();

            $data = json_decode($content, true);


            $user = new User();
            $name = $data['admin_user[name]'] ?? null;
            $lastName = $data['admin_user[lastName]'] ?? null;
            $email = $data['admin_user[email]'] ?? null;
            $society = $data['admin_user[society]'] ?? null;
            $roles = $data['admin_user[roles][]'] ?? [];

            $society = $this->entityManagerInterface->getRepository(Society::class)->findOneBy(['id' => $society]);


            $user->setEmail($email);
            $user->setName($name);
            $user->setLastName($lastName);
            $user->setPassword($this->getUser()->getSociety()->getName() . uniqid());
            $user->setCreatedAt(new \DateTime());
            $user->setSociety($society ?? $this->getUser()->getSociety());
            $user->setIsVerified(false);

            if ($userRepository->findOneBy(['email' => $email])) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "Le e-mail est déja pris"
                ));
            }

            if (ROLE_ADMIN === $roles) {
                $this->addFlash('danger', 'Vous ne pouvez pas attribuer le rôle administrateur');
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "Vous ne pouvez pas vous attribuer le rôle administrateur"
                ));
            }

            $this->addFlash('success', 'Un utilisateur a été crée');

            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();
            return new JsonResponse(array(
                'code' => 200,
                'success' => false,
                'message' => "L'utilisateur a bien été crée"
            ));
        }
        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

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
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (in_array(ROLE_ADMIN, $form->get('roles')->getData())) {
                $this->addFlash('danger', 'Vous ne pouvez pas attribuer le rôle administrateur');
                return $this->redirectToRoute('admin_user_index');
            }
            $this->entityManagerInterface->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('admin_user_index');
        }
        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
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
