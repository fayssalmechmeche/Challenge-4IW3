<?php

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailjetService
{
    const TEMPLATE_REGISTER = 5538680;

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

    public function sendEmail(string $email, string $name, int $templateID, $data = []): bool
    {
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
                ]
            ]
        ];
        $response = $this->client->post(Resources::$Email, ['body' => $body]);
        return $response->success();
    }
}
