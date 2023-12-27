<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use const App\Entity\ROLE_ACCOUNTANT;
use const App\Entity\ROLE_ADMIN;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
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

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
