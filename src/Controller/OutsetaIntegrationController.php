<?php

declare(strict_types=1);

namespace UserFrosting\Sprinkle\UfOutsetaIntegration\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class OutsetaIntegrationController
{
    /**
     * test.
     * Request type: GET.
     *
     * @param Request  $request
     * @param Response $response
     * @param Twig     $view
     */
    public function __invoke(Request $request, Response $response, Twig $view): Response
    {
        return $view->render($response, 'pages/uf-outseta-integration.html.twig', $request->getQueryParams());
    }
}
