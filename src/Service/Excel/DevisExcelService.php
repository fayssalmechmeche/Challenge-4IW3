<?php

namespace App\Service\Excel;

use DateTime;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DevisExcelService
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
        $this->sheet->MergeCells('G1:H1');
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

    public function generateXlsx($devis)
    {

        $this->setData($devis);
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
            'devis' . '_' . $now . '.xlsx'
        );
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        return $response;
    }

    public function generatePDF($devis)
    {
        $this->setData($devis);
        $this->getWriterPdf()->setIncludeCharts(true);
        $this->getWriterPdf()->getPreCalculateFormulas(true);
        $now = (new DateTime())->format('Y-m-d_H-i-s');
        $name = $this->parameter->get('kernel.project_dir') . '/var/' . 'devis' . '_' . $now;
        $this->getWriterPdf()->save($name . '.pdf');
        $response = new BinaryFileResponse($name . '.pdf');

        // Set headers for file download
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'devis' . '_' . $now . '.pdf'
        );
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

    public function setData($devis)
    {
        $this->getStyle();
        $this->sheet->setCellValue('A1', "Numéro de devis");
        $this->sheet->setCellValue('C1', 'Client');
        $this->sheet->setCellValue('D1', "Sujet");
        $this->sheet->setCellValue('E1', "Prix total HT");
        $this->sheet->setCellValue('F1', "Total Due HT");
        $this->sheet->setCellValue('G1', "Crée le");

        $line = 2;
        /** @var Devis $value */
        foreach ($devis as $key => $value) {

            $this->sheet->MergeCells('A' . $line . ':B' . $line);
            $this->sheet->getStyle('A' . $line . ':H' . $line)->getAlignment()->setHorizontal('center');
            $this->sheet->MergeCells('G' . $line . ':H' . $line);
            $customer = $value->getCustomer()->getNameSociety() ?: $value->getCustomer()->getName() . ' ' . $value->getCustomer()->getLastName();
            $this->sheet->setCellValue('A' . $line, $value->getDevisNumber());
            $this->sheet->setCellValue('C' . $line, $customer);
            $this->sheet->setCellValue('D' . $line, $value->getSubject());
            $this->sheet->setCellValue('E' . $line, $value->getTotalPrice());
            $this->sheet->setCellValue('F' . $line, $value->getTotalDuePrice());
            $this->sheet->setCellValue('G' . $line, $value->getCreatedAt()->format('Y-m-d H:i:s'));
            $line++;
        }
        $this->sheet->setCellValue('A' . $line, "Total");
        $this->sheet->setCellValue('E' . $line, "=SUM(E2:E" . ($line - 1) . ")");
        $this->sheet->setCellValue('F' . $line, "=SUM(F2:F" . ($line - 1) . ")");
        $this->sheet->getStyle('A' . $line . ':H' . $line)->getAlignment()->setHorizontal('center');
    }
}
