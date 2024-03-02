<?php

namespace App\Service\Excel;

use DateTime;
use App\Entity\DevisProduct;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DevisProductExcelService
{
    private $spreadsheet;
    private $sheet;
    private $parameter;
    public function __construct(ParameterBagInterface $parameter)
    {
        $this->parameter = $parameter;
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    public function getStyle()
    {
        $this->sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $this->sheet->getStyle('A1:H1')->getAlignment()->setHorizontal('center');
        $this->sheet->MergeCells('A1:B1');
    }

    public function getReader()
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(false);
        return $reader;
    }

    public function getWriterXslx()
    {
        $writer = new XlsxWriter($this->spreadsheet);
        return $writer;
    }

    public function getWriterPdf()
    {
        $writer = new Dompdf($this->spreadsheet);
        return $writer;
    }

    public function generateXlsx($devisProduct)
    {

        $this->setData($devisProduct);
        $this->getWriterXslx()->setIncludeCharts(true);
        $this->getWriterXslx()->getPreCalculateFormulas(true);
        $this->getWriterXslx()->setOffice2003Compatibility(true);
        $now = (new DateTime())->format('Y-m-d_H-i-s');
        $name = $this->parameter->get('kernel.project_dir') . '/var/' . 'devis' . '_' . $now;
        $this->getWriterXslx()->save($name . '.xlsx');
        // Create BinaryFileResponse
        $response = new BinaryFileResponse($name . '.xlsx');

        // Set headers for file download
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'devisProduct' . '_' . $now . '.xlsx'
        );
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        return $response;
    }

    public function generatePDF($devisProduct)
    {
        $this->setData($devisProduct);
        $this->getWriterPdf()->setIncludeCharts(true);
        $this->getWriterPdf()->getPreCalculateFormulas(true);
        $now = (new DateTime())->format('Y-m-d_H-i-s');
        $name = $this->parameter->get('kernel.project_dir') . '/var/' . 'devis' . '_' . $now;
        $this->getWriterPdf()->save($name . '.pdf');
        $response = new BinaryFileResponse($name . '.pdf');

        // Set headers for file download
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'devisProduct' . '_' . $now . '.pdf'
        );
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

    public function setData($devisProduct)
    {
        $this->getStyle();
        $this->sheet->setCellValue('A1', "id");
        $this->sheet->setCellValue('C1', 'Nom');
        $this->sheet->setCellValue('D1', "Quantité");
        $this->sheet->setCellValue('E1', "Prix");
        $this->sheet->setCellValue('F1', "Prix total");
        $this->sheet->setCellValue('G1', "Catégorie");

        $line = 2;

        // Initialise un tableau pour filtrer les doublons
        $tabFiltered = [];

        /** @var DevisProduct $value */
        foreach ($devisProduct as $key => $value) {
            $productName = $value->getProduct()->getName();
            if (array_key_exists($productName, $tabFiltered)) {
                $tabFiltered[$productName] += $value->getQuantity();
            } else {
                $tabFiltered[$productName] = $value->getQuantity();
            }
        }

        foreach ($tabFiltered as $productName => $quantity) {
            $this->sheet->MergeCells('A' . $line . ':B' . $line);
            $this->sheet->getStyle('A' . $line . ':H' . $line)->getAlignment()->setHorizontal('center');

            // Trouver le premier élément dans $devisProduct correspondant à ce nom de produit
            $matchingDevisProduct = null;
            foreach ($devisProduct as $value) {
                if ($value->getProduct()->getName() === $productName) {
                    $matchingDevisProduct = $value;
                    break;
                }
            }

            if ($matchingDevisProduct !== null) {
                $this->sheet->setCellValue('A' . $line, $matchingDevisProduct->getId());
                $this->sheet->setCellValue('C' . $line, $productName);
                $this->sheet->setCellValue('D' . $line, $quantity);
                $this->sheet->setCellValue('E' . $line, $matchingDevisProduct->getPrice() / 100);
                $this->sheet->setCellValue('F' . $line, ($matchingDevisProduct->getPrice() / 100) * $quantity);
                $this->sheet->setCellValue('G' . $line, $matchingDevisProduct->getProduct()->getCategory());
                $line++;
            }
        }

        $this->sheet->setCellValue('A' . $line, "Total");
        $this->sheet->setCellValue('D' . $line, "=SUM(D2:D" . ($line - 1) . ")");
        $this->sheet->setCellValue('E' . $line, "=SUM(E2:E" . ($line - 1) . ")");
        $this->sheet->setCellValue('F' . $line, "=SUM(F2:F" . ($line - 1) . ")");
        $this->sheet->getStyle('A' . $line . ':H' . $line)->getAlignment()->setHorizontal('center');
    }
}
