<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use App\Form\Admin\SocietyType;
use App\Form\ProfileSocietyFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile_show')]
    public function show(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        return $this->redirectToRoute('profile_edit');
    }

    #[Route('/profile/edit', name: 'profile_edit')]
    public function edit(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        $society = $user->getSociety();
        $societyForm = $this->createForm(ProfileSocietyFormType::class, $society);
        $societyForm->handleRequest($request);

        if ($form->isSubmitted()) {
            $userData = $form->getData();
            $errors = [];
            if (empty($userData->getName())) {
                $errors[] = "Le Prénom ne peut pas être vide.";
            } elseif (strlen($userData->getName()) < 2 || strlen($userData->getName()) > 50) {
                $errors[] = "Le Prénom doit contenir entre 2 et 50 caractères.";
            }
            if (empty($userData->getLastName())) {
                $errors[] = "Le Nom ne peut pas être vide.";
            } elseif (strlen($userData->getLastName()) < 2 || strlen($userData->getLastName()) > 50) {
                $errors[] = "Le Nom doit contenir entre 2 et 50 caractères.";
            }
            if (empty($userData->getEmail())) {
                $errors[] = "L'email ne peut pas être vide.";
            } elseif ($entityManager->getRepository(User::class)->findOneBy(['email' => $userData->getEmail()]) !== $user) {
                $errors[] = "L'adresse email n'est pas valide.";
            } elseif (!filter_var($userData->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'adresse email n'est pas valide.";
            }
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->redirectToRoute('profile_edit');
            } else {
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Profil mis à jour avec succès.');
                return $this->redirectToRoute('profile_edit');
            }
        }

        if ($societyForm->isSubmitted() && $societyForm->isValid()) {
            $societyData = $societyForm->getData();
            $errors = [];

            $fileName = $societyForm->get('logo')->getData();
            if ($fileName) {
                $originalFilename = pathinfo($fileName->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $fileName->guessExtension();
                try {
                    $fileName->move(
                        $this->getParameter('society_logo_directory'),
                        $newFilename
                    );
                    $old = $society->getLogo();

                    if ($old) {
                        unlink($this->getParameter('society_logo_directory') . '/' . $old);
                    }
                } catch (FileException $e) {
                    $this->addFlash('danger', "Une erreur est survenue lors de l'upload de l'image");
                }
                $society->setLogo($newFilename);
            }
            if (empty($societyData->getName())) {
                $errors[] = "Le nom de la société ne peut pas être vide.";
            } elseif (strlen($societyData->getName()) < 2 || strlen($societyData->getName()) > 100) {
                $errors[] = "Le nom de la société doit contenir entre 2 et 100 caractères.";
            }
            if (empty($societyData->getAddress())) {
                $errors[] = "L'adresse ne peut pas être vide.";
            } elseif (strlen($societyData->getAddress()) < 5 || strlen($societyData->getAddress()) > 255) {
                $errors[] = "L'adresse doit contenir entre 5 et 255 caractères.";
            }
            if (empty($societyData->getPhone())) {
                $errors[] = "Le numéro de téléphone ne peut pas être vide.";
            } elseif (!preg_match('/^\d+$/', $societyData->getPhone())) { // Utilisez \d+ pour vérifier la présence de chiffres uniquement
                $errors[] = "Le numéro de téléphone doit contenir uniquement des chiffres.";
            }
            if (empty($societyData->getEmail())) {
                $errors[] = "L'email ne peut pas être vide.";
            } elseif (!filter_var($societyData->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'adresse email n'est pas valide.";
            }
            if (empty($societyData->getSiret())) {
                $errors[] = "Le SIRET ne peut pas être vide.";
            } elseif (!preg_match('/^[0-9]{14}$/', $societyData->getSiret())) {
                $errors[] = "Le SIRET doit contenir 14 chiffres.";
            }
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->redirectToRoute('profile_edit');
            } else {
                $entityManager->persist($society);
                $entityManager->flush();
                $this->addFlash('success', 'Société mise à jour avec succès.');
                return $this->redirectToRoute('profile_edit');
            }
        }


        return $this->render('profile/edit.html.twig', [
            'userForm' => $form->createView(),
            'societyForm' => $societyForm->createView(),
            'user' => $user,
            'society' => $society,
        ]);
    }
}
