<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Services\OutsetaService;

class OutsetaDemoController
{
    public function page(Request $request, Response $response, OutsetaService $outseta): Response
    {
        $person = $outseta->getPersonByEmail('info@urosaleksic.com');

        if ($person) {
            $payload = [
                'message' => 'Found user in Outseta!',
                'name'    => $person['FullName'],
                'uid'     => $person['Uid'],
            ];
        } else {
            $payload = ['message' => 'User not found.'];
        }

        return $this->render($response, 'pages/outseta-demo.html.twig', $payload);
    }
}