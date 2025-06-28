<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $group) {
    $group->get('/uf-outseta-integration', function (Request $request, Response $response) {
        // This assumes you have a 'pages/uf-outseta-integration.html.twig' template
        $this->get('view')->render($response, 'pages/uf-outseta-integration.html.twig');
        return $response;
    });
};
