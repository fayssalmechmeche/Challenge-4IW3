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
    public function index(Request $request): Response
    {
        $usersVerified = $this->entityManagerInterface->getRepository(User::class)->countUsersVerfied(true);
        $usersNotVerified = $this->entityManagerInterface->getRepository(User::class)->countUsersVerfied(false);
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'code' => 200,
                'success' => true,
                'data' => [
                    'countUsers' => $usersNotVerified + $usersVerified,
                    'countUsersVerified' => $usersVerified,
                    'countUsersNotVerified' => $usersNotVerified

                ]
            ));
        }
        return $this->render('admin/user/index.html.twig', [
            'countUsers' => $usersNotVerified + $usersVerified,
            'countUsersVerified' => $usersVerified,
            'countUsersNotVerified' => $usersNotVerified
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
    public function new(Request $request)
    {
        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);
        if ($request->isXmlHttpRequest()) {
            $content = $request->getContent();
            $data = json_decode($content, true);
            $user = new User();
            $this->_setDataUser($user, $data);
            return new JsonResponse(array(
                'code' => 200,
                'success' => true,
                'message' => "L'utilisateur a bien été crée"
            ));
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
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);
        if ($request->isXmlHttpRequest()) {
            $content = $request->getContent();
            $data = json_decode($content, true);
            $this->_setDataUser($user, $data);
            return new JsonResponse(array(
                'code' => 200,
                'success' => true,
                'message' => "Utilisateur modifié avec succès"
            ));
        }
        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/delete/{id}/{token}', name: 'delete')]
    public function delete(User $user, string $token): Response
    {
        dump($token);
        if ($this->isCsrfTokenValid('delete-users' . $user->getId(), $token)) {
            $this->entityManagerInterface->remove($user);
            $this->entityManagerInterface->flush();
            return new JsonResponse(array(
                'code' => 200,
                'success' => true,
                'message' => "Utilisateur a été supprimé avec succès"
            ));
        }
        return new JsonResponse(array(
            'code' => 200,
            'success' => false,
            'message' => "Token invalide"
        ));
    }

    public function _setDataUser($user, $data)
    {
        $name = $data['admin_user[name]'] ?? null;
        $lastName = $data['admin_user[lastName]'] ?? null;
        $email = $data['admin_user[email]'] ?? null;
        $society = $data['admin_user[society]'] ?? null;
        $roles = $data['admin_user[roles][]'] ?? [];

        $society = $this->entityManagerInterface->getRepository(Society::class)->findOneBy(['id' => $society]);
        if (!$society) {
            return new JsonResponse(array(
                'code' => 200,
                'success' => false,
                'message' => "La societé n'existe pas"
            ));
        }


        $user->setEmail($email);
        $user->setName($name);
        $user->setLastName($lastName);
        $user->setPassword($this->getUser()->getSociety()->getName() . uniqid());
        $user->setCreatedAt(new \DateTime());
        $user->setSociety($society ?? $this->getUser()->getSociety());
        $user->setRoles([$roles]);
        $user->setIsVerified(false);

        $isUserExist = $this->entityManagerInterface->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($isUserExist && $isUserExist->getId() != $user->getId()) {
            return new JsonResponse(array(
                'code' => 200,
                'success' => false,
                'message' => "L'e-mail est déja pris"
            ));
        }

        if (ROLE_ADMIN === $roles) {
            return new JsonResponse(array(
                'code' => 200,
                'success' => false,
                'message' => "Vous ne pouvez pas vous attribuer le rôle administrateur"
            ));
        }

        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->flush();
    }
}
