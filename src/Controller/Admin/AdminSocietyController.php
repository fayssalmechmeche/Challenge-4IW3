<?php

namespace App\Controller\Admin;

use App\Entity\Society;
use App\Form\Admin\SocietyType;
use App\Repository\UserRepository;
use App\Repository\DevisRepository;
use App\Repository\SocietyRepository;
use App\Service\Stripe\StripeHelper;
use App\Service\Stripe\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/society', name: 'admin_society_')]
class AdminSocietyController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'code' => 200,
                'success' => true,
                'data' => [
                    'countSocieties' => count($this->entityManagerInterface->getRepository(Society::class)->findAll())
                ]
            ));
        }
        return $this->render('admin/society/index.html.twig', [
            'societies' => $this->entityManagerInterface->getRepository(Society::class)->findAll(),
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
    public function new(Request $request, StripeService $stripeService)
    {
        $society = new Society();
        $form = $this->createForm(SocietyType::class, $society);
        $form->handleRequest($request);
        if ($request->isXmlHttpRequest()) {
            $content = $request->getContent();
            $data = json_decode($content, true);

            if (
                isset($data['society[name]']) && $data['society[name]'] == ""
                || isset($data['society[address]']) && $data['society[address]'] == ""
                || isset($data['society[phone]']) && $data['society[phone]'] == ""
                || isset($data['society[email]']) && $data['society[email]'] == ""
                || isset($data['society[siret]']) && $data['society[siret]'] == ""
                || isset($data['society[_token]']) && $data['society[_token]'] == ""
            ) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "Tous les champs sont requis"
                ));
            }

            foreach ($data as $key => $value) {
                switch ($key) {
                    case "society[name]":
                        if (strlen($value) < 2)
                            return new JsonResponse(array(
                                'code' => 200,
                                'success' => false,
                                'message' => "Le nom de la société doit contenir au moins 2 caractères"
                            ));

                        break;
                    case "society[address]":
                        if (strlen($value) < 5)
                            return new JsonResponse(array(
                                'code' => 200,
                                'success' => false,
                                'message' => "L'adresse de la société doit contenir au moins 5 caractères"
                            ));
                        break;
                    case "society[phone]":
                        if (strlen($value) < 10)
                            return new JsonResponse(array(
                                'code' => 200,
                                'success' => false,
                                'message' => "Le téléphone de la société doit contenir au moins 10 chiffres"
                            ));
                        break;
                    case "society[email]":
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL))
                            return new JsonResponse(array(
                                'code' => 200,
                                'success' => false,
                                'message' => "L'email de la société est invalide"
                            ));
                        break;
                }
            }
            if (!$this->isCsrfTokenValid("society", $data['society[_token]'])) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "Token invalid"
                ));
            }
            $this->_setDataSociety($society, $data);
            $stripeService->createCustomer($society);
            return new JsonResponse(array(
                'code' => 200,
                'success' => true,
                'message' => "La société a bien été crée"
            ));
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
        if ($request->isXmlHttpRequest()) {
            $content = $request->getContent();
            $data = json_decode($content, true);

            if (!$this->isCsrfTokenValid("society", $data['society[_token]'])) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "Token invalid"
                ));
            }
            $this->_setDataSociety($society, $data);
            return new JsonResponse(array(
                'code' => 200,
                'success' => true,
                'message' => "La société a bien été modifié"
            ));
        }
        return $this->render('admin/society/edit.html.twig', [
            'society' => $society,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}/{token}', name: 'delete')]
    public function delete(Society $society, string $token): Response
    {
        if ($this->isCsrfTokenValid('delete-society' . $society->getId(), $token)) {

            if ($society == $this->getUser()->getSociety()) {
                return new JsonResponse(array(
                    'code' => 200,
                    'success' => false,
                    'message' => "Vous ne pouvez pas supprimer votre société"
                ));
            }
            $users = $society->getUsers();
            foreach ($users as $user) {
                $this->entityManagerInterface->remove($user);
            }
            $this->entityManagerInterface->remove($society);
            $this->entityManagerInterface->flush();
            return new JsonResponse(array(
                'code' => 200,
                'success' => true,
                'message' => "La société a été supprimé avec succès"
            ));
        }
        return new JsonResponse(array(
            'code' => 200,
            'success' => false,
            'message' => "Token invalide"
        ));
    }

    public function _setDataSociety(Society $society, $data)
    {
        $name = $data['society[name]'];
        $addess = $data['society[address]'];
        $phone = $data['society[phone]'];
        $email = $data['society[email]'];
        $siret = $data['society[siret]'];

        $society->setName($name);
        $society->setAddress($addess);
        $society->setPhone($phone);
        $society->setEmail($email);
        $society->setSiret($siret);

        $this->entityManagerInterface->persist($society);
        $this->entityManagerInterface->flush();
    }
}
