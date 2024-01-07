<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Society;
use App\Security\EmailVerifier;
use App\Service\MailjetService;
use const App\Entity\ROLE_ADMIN;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use const App\Entity\ROLE_SOCIETY;
use Symfony\Component\Mime\Address;
use const App\Entity\ROLE_ACCOUNTANT;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/check/email', name: 'check_email')]
    public function check(UserRepository $userRepository, Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'code' => 403,
                'success' => false,
                'message' => "La requête n'est pas une requête ajax"
            ));
        }

        if ($userRepository->findOneBy(['email' => $request->request->get('email')])) {
            return new JsonResponse(array(
                'code' => 200,
                'success' => false,
                'message' => "L'utilisateur existe déja"
            ));
        } else {
            return new JsonResponse(array(
                'code' => 200,
                'success' => true,
                'message' => "Aucun utilisateur existe"
            ));
        }
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailjetService $mailjet): Response
    {
        if ($this->getUser()) {
            switch ($this->getUser()->getRoles()) {
                case in_array(ROLE_ADMIN, $this->getUser()->getRoles()):
                    return $this->redirectToRoute('admin_index');
                case in_array(ROLE_ACCOUNTANT, $this->getUser()->getRoles()):
                    return $this->redirectToRoute('home_index');
                default:
                    return $this->redirectToRoute('home_index');
            }
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = uniqid();

            $society = new Society();
            $society->setName($form->get('societyForm')->get('name')->getData());
            $society->setAddress($form->get('societyForm')->get('address')->getData());
            $society->setPhone($form->get('societyForm')->get('phone')->getData());
            $society->setEmail($form->get('societyForm')->get('email')->getData());
            $society->setSiret($form->get('societyForm')->get('siret')->getData());
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setIsVerified(false);
            $user->setRoles([ROLE_SOCIETY]);
            $user->setCreatedAt(new \DateTime());
            $user->setSociety($society);
            $user->setToken($token);
            $entityManager->persist($society);
            $entityManager->persist($user);
            $entityManager->flush();
            $link = $this->generateUrl('app_register_confirm', [
                'id' => $user->getId(),
                'token' => $token
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $mailjet->sendEmail($user->getEmail(), $user->getName() . " " . $user->getLastName(), MailjetService::TEMPLATE_REGISTER, [
                'confirmation_link' => $link
            ]);
            return $this->redirectToRoute('app_login');
        } elseif ($form->isSubmitted() && !$form->isValid()) {

            $this->addFlash('verify_email_error', $form->getErrors(true));
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/{id}/{token}', name: 'app_register_confirm')]
    public function confirm(User $user, String $token, EntityManagerInterface $entityManager)
    {
        if ($user->getToken() == $token) {
            $user->setIsVerified(true);
            $user->setToken(null);

            $entityManager->flush();
            $this->addFlash('success', 'Votre compte a bien été activé');
            return $this->redirectToRoute('app_login');
        } else {
            $this->addFlash('danger', 'Une erreur est survenue lors de l\'activation de votre compte');
            return $this->redirectToRoute('app_register');
        }
    }
}
