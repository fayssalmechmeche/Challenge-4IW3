<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\DevisProduct;
use App\Service\Excel\DevisExcelService;
use App\Service\Excel\DevisProductExcelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/excel')]
class ExcelController extends AbstractController
{

    #[Route('/generate/xlsx', name: 'app_devis_generate_xlsx', methods: ['POST'])]
    public function generateXlsx(DevisExcelService $DevisExcelService, Request $request, EntityManagerInterface $entityManager): Response
    {
        $devis = $entityManager->getRepository(Devis::class)->findBy(['user' => $this->getUser()]);

        if ($request->isXmlHttpRequest()) {
            return  $DevisExcelService->generateXlsx($devis);
        } else {
            return $this->json([
                'code' => 401,
                'message' => 'Requête non autorisée !',
            ], 401);
        }
    }

    #[Route('/generate/pdf', name: 'app_devis_generate_pdf', methods: ['POST'])]
    public function generatePdf(DevisExcelService $DevisExcelService, Request $request, EntityManagerInterface $entityManager): Response
    {
        $devis = $entityManager->getRepository(Devis::class)->findBy(['user' => $this->getUser()]);
        if ($request->isXmlHttpRequest()) {
            return  $DevisExcelService->generatePDF($devis);
        } else {
            return $this->json([
                'code' => 401,
                'message' => 'Requête non autorisée !',
            ], 401);
        }
    }

    #[Route('/generate/product/xlsx', name: 'excel_devis_product_xlsx')]
    public function generateExcelDevisProduct(DevisProductExcelService $devisProductExcelService, Request $request, EntityManagerInterface $entityManager): Response
    {
        $devis = $entityManager->getRepository(Devis::class)->findBy(['user' => $this->getUser()]);
        $tabDevisProduct = [];
        foreach ($devis as $devis) {
            $devisProduct = $entityManager->getRepository(DevisProduct::class)->findBy(['devis' => $devis]);
            foreach ($devisProduct as $devisProduct) {
                $tabDevisProduct[] = $devisProduct;
            }
        }
        dump($tabDevisProduct);
        if ($request->isXmlHttpRequest()) {
            return $devisProductExcelService->generateXlsx($tabDevisProduct);
        } else {
            return $this->json([
                'code' => 401,
                'message' => 'Requête non autorisée !',
            ], 401);
        }
    }

    #[Route('/generate/product/pdf', name: 'excel_devis_product_pdf')]
    public function generatePdfDevisProduct(DevisProductExcelService $devisProductExcelService, Request $request, EntityManagerInterface $entityManager): Response
    {
        $devis = $entityManager->getRepository(Devis::class)->findBy(['user' => $this->getUser()]);
        $tabDevisProduct = [];
        foreach ($devis as $devis) {
            $devisProduct = $entityManager->getRepository(DevisProduct::class)->findBy(['devis' => $devis]);
            foreach ($devisProduct as $devisProduct) {
                $tabDevisProduct[] = $devisProduct;
            }
        }
        dump($tabDevisProduct);
        if ($request->isXmlHttpRequest()) {
            return $devisProductExcelService->generatePDF($tabDevisProduct);
        } else {
            return $this->json([
                'code' => 401,
                'message' => 'Requête non autorisée !',
            ], 401);
        }
    }
}
