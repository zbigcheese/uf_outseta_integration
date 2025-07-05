<?php

declare(strict_types=1);

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration;

use Slim\App;
use UserFrosting\Routes\RouteDefinitionInterface;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller\OutsetaIntegrationController;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller\OutsetaDemoController;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller\TeamController;
use UserFrosting\Sprinkle\Account\Authenticate\AuthGuard;

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

        $app->group('/team', function ($app) {
            // GET /team - Displays the management page
            $app->get('', [TeamController::class, 'page']);
            // Route for the form submission to add a teammate
            $app->post('/add', [TeamController::class, 'addTeammate']);
            // Route to remove a teammate. The {id} is the UserFrosting user ID.
            $app->delete('/remove/{id}', [TeamController::class, 'removeTeammate']);
        })->add(AuthGuard::class); // Protect this whole group with standard login middleware
    }
}
