<?php

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailjetService
{
    const TEMPLATE_REGISTER = 5603301;
    const TEMPLATE_CONFIRM_REGISTER = 5603317;
    const TEMPLATE_FORGET_PASSWORD = 5603341;
    const TEMPLATE_DEVIS = 5603324;
    const TEMPLATE_INVOICE = 5603327;
    const TEMPLATE_INVOICE_NO_DEPOSIT = 5719613;


    private $client;
    public function __construct(private ParameterBagInterface $parameterBag)
    {
        $this->client = new Client(
            $this->parameterBag->get("mailjet_public"),
            $this->parameterBag->get("mailjet_private"),
            true,
            ['version' => 'v3.1']
        );
    }

    public function sendEmail(string $email, string $name, int $templateID, $data = [], string $pdfName = null): bool
    {
        $attachments = $pdfName ?  [
            [
                'ContentType' => "application/pdf",
                'Filename' => "document.pdf",
                'ContentID' => uniqid(),
                'Base64Content' => base64_encode(file_get_contents($this->parameterBag->get("pdf_directory") . $pdfName . '.pdf'))
            ]
        ] : null;
        $body = [
            'Messages' => [
                [
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $name
                        ]
                    ],
                    'TemplateID' => $templateID,
                    "TemplateLanguage" => true,
                    'Variables' => $data,
                    'Attachments' => $attachments

                ]
            ]
        ];
        $response = $this->client->post(Resources::$Email, ['body' => $body]);
        return $response->success();
    }
}
