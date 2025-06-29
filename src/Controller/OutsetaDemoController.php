<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Services\OutsetaService;
use Slim\Views\Twig;

class OutsetaDemoController
{

    /**
     * test.
     * Request type: GET.
     *
     * @param Request  $request
     * @param Response $response
     * @param Twig     $view
     */

    public function page(Request $request, Response $response, OutsetaService $outseta, Twig $view): Response
    {
        $person = $outseta->getPersonByEmail('dejan.otasevic@gmail.com');

        if ($person) {
            $payload = [
                'message' => 'Found user in Outseta!',
                'name'    => $person['FullName'],
                'uid'     => $person['Uid'],
            ];
        } else {
            $payload = ['message' => 'User not found.'];
        }

        return $view->render($response, 'pages/outseta-demo.html.twig', 
            [
                'payload' => $payload,
                'testvar' => 'test123'

            ]
        );
    }
}