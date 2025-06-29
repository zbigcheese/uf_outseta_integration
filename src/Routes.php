<?php

declare(strict_types=1);

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration;

use Slim\App;
use UserFrosting\Routes\RouteDefinitionInterface;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller\OutsetaIntegrationController;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller\OutsetaDemoController;

class Routes implements RouteDefinitionInterface
{
    public function register(App $app): void
    {
        $app->get('/uf-outseta-integration', OutsetaIntegrationController::class);
        $app->get('/uf-outseta-demo', [OutsetaDemoController::class, 'page']);

        $app->group('/api/outseta', function ($app) {
            // This route will handle all incoming webhooks from Outseta
            $app->post('/webhooks', [WebhookController::class, 'process']);
        });
    }
}
