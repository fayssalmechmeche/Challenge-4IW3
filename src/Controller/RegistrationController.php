<?php

namespace App\Controller;

use App\Entity\Society;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

use const App\Entity\ROLE_ACCOUNTANT;
use const App\Entity\ROLE_ADMIN;
use const App\Entity\ROLE_SOCIETY;

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
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
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

            $user->setIsVerified(true);
            $user->setRoles([ROLE_SOCIETY]);
            $user->setCreatedAt(new \DateTime());
            $user->setSociety($society);
            $entityManager->persist($society);
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            // $this->emailVerifier->sendEmailConfirmation(
            //     'app_verify_email',
            //     $user,
            //     (new TemplatedEmail())
            //         ->from(new Address('mailer@your-domain.com', 'Acme Mail Bot'))
            //         ->to($user->getEmail())
            //         ->subject('Please Confirm your Email')
            //         ->htmlTemplate('registration/confirmation_email.html.twig')
            // );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        } elseif ($form->isSubmitted() && !$form->isValid()) {

            $this->addFlash('verify_email_error', $form->getErrors(true));
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
