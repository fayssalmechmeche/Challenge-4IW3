<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Society;

use const App\Entity\ROLE_HEAD;
use const App\Entity\ROLE_ADMIN;
use App\Form\Admin\AdminUserType;
use App\Entity\ResetPasswordRequest;

use Doctrine\ORM\EntityManagerInterface;
use App\Controller\ResetPasswordController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
#[IsGranted('ROLE_HEAD')]
class UserController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
    }
    #[Route('/', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', []);
    }

    #[Route('/api', name: 'api_user_index', methods: ['GET'])]
    public function apiIndex(CsrfTokenManagerInterface $tokenManager): Response
    {
        $users = $this->entityManagerInterface->getRepository(User::class)->findBy(['society' => $this->getUser()->getSociety()]);
        $data = [];
        foreach ($users as $user) {
            if (in_array(ROLE_HEAD, $user->getRoles()) || in_array(ROLE_ADMIN, $user->getRoles())) {
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
    public function new(Request $request,)
    {
        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);
        if ($request->isXmlHttpRequest()) {
            $content = $request->getContent();
            $data = json_decode($content, true);
            if (
                isset($data['admin_user[email]']) && $data['admin_user[email]'] == ""
                || isset($data['admin_user[name]']) && $data['admin_user[name]'] == ""
                || isset($data['admin_user[lastName]']) && $data['admin_user[lastName]'] == ""
                || isset($data['admin_user[society]']) && $data['admin_user[society]'] == ""
                || isset($data["admin_user[roles][]"]) && $data["admin_user[roles][]"] == ""
            ) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "Tous les champs sont obligatoires"
                ));
            }

            if (!isset($data["admin_user[roles][]"]) || $data["admin_user[roles][]"] == "" || empty($data["admin_user[roles][]"]) || !$data["admin_user[roles][]"]) {
                return new JsonResponse(array(
                    'code' => 401,
                    'success' => false,
                    'message' => "Le rôle est obligatoire"
                ));
            }
            if (!$this->isCsrfTokenValid("admin_user", $data['admin_user[_token]'])) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "Token invalid"
                ));
            }
            if (!filter_var($data['admin_user[email]'], FILTER_VALIDATE_EMAIL)) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "L'e-mail n'est pas valide"
                ));
            }
            if ($this->entityManagerInterface->getRepository(User::class)->findOneBy(['email' => $data['admin_user[email]']])) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "L'utilisateur existe déja"
                ));
            }
            $user->setIsVerified(false);
            $this->_setDataUser($user, $data, false);
            $response = $this->forward('App\Controller\ResetPasswordController::processSendingPasswordResetEmail', [
                'emailFormData' => $data['admin_user[email]'],
                'isReset' => false
            ]);
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
        $society = $this->getUser()->getSociety();
        if ($society->getId() != $user->getSociety()->getId()) {
            return $this->redirectToRoute('app_user');
        }

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(User $user, Request $request): Response
    {
        $society = $this->getUser()->getSociety();
        if ($society->getId() != $user->getSociety()->getId()) {
            return $this->redirectToRoute('app_user');
        }

        $form = $this->createForm(AdminUserType::class, $user, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);
        if ($request->isXmlHttpRequest()) {
            $content = $request->getContent();
            $data = json_decode($content, true);
            if (
                isset($data['admin_user[email]']) && $data['admin_user[email]'] == ""
                || isset($data['admin_user[name]']) && $data['admin_user[name]'] == ""
                || isset($data['admin_user[lastName]']) && $data['admin_user[lastName]'] == ""
                || isset($data['admin_user[society]']) && $data['admin_user[society]'] == ""
                || isset($data["admin_user[roles][]"]) && $data["admin_user[roles][]"] == ""
            ) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "Tous les champs sont obligatoires"
                ));
            }


            $isUserExist = $this->entityManagerInterface->getRepository(User::class)->findOneBy(['email' => $data['admin_user[email]']]);
            if ($isUserExist && $isUserExist->getId() != $user->getId()) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "L'e-mail est déja utilisé"
                ));
            }
            if (ROLE_HEAD === $data["admin_user[roles][]"] || ROLE_ADMIN === $data["admin_user[roles][]"]) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "Vous ne pouvez pas attribuer le rôle chef ou admin"
                ));
            }

            if (!isset($data["admin_user[roles][]"]) || $data["admin_user[roles][]"] == "" || empty($data["admin_user[roles][]"]) || !$data["admin_user[roles][]"]) {
                return new JsonResponse(array(
                    'code' => 401,
                    'success' => false,
                    'message' => "Le rôle est obligatoire"
                ));
            }
            if (!$this->isCsrfTokenValid("admin_user", $data['admin_user[_token]'])) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "Token invalid"
                ));
            }

            $this->_setDataUser($user, $data, true);
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

    public function _setDataUser(User $user, $data, $edit)
    {
        $name = $data['admin_user[name]'] ?? null;
        $lastName = $data['admin_user[lastName]'] ?? null;
        $email = $data['admin_user[email]'] ?? null;
        $roles = $data['admin_user[roles][]'] ?? [];


        $user->setEmail($email);
        $user->setName($name);
        $user->setLastName($lastName);
        $user->setPassword($this->getUser()->getSociety()->getName() . uniqid());
        $user->setCreatedAt(new \DateTime());
        $user->setSociety($this->getUser()->getSociety());
        $user->setRoles([$roles]);
        if ($edit) {
            $user->setUpdatedAt(new \DateTime());
        }
        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->flush();
    }

    #[Route('/delete/{id}/{token}', name: 'delete')]
    public function delete(User $user, string $token): Response
    {
        $society = $this->getUser()->getSociety();
        if ($society->getId() != $user->getSociety()->getId()) {
            return $this->redirectToRoute('app_user');
        }
        
        if ($this->isCsrfTokenValid('delete-users' . $user->getId(), $token)) {

            $tokens = $this->entityManagerInterface->getRepository(ResetPasswordRequest::class)->findBy(['user' => $user]);
            foreach ($tokens as $resetPasswordRequest) {
                $this->entityManagerInterface->remove($resetPasswordRequest);
            }

            $this->entityManagerInterface->flush();

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
}
