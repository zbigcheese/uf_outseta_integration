<?php

declare(strict_types=1);

namespace UserFrosting\Sprinkle\UfOutsetaIntegration;

//use Psr\Http\Message\ResponseInterface as Response;
//use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Controller\OutsetaIntegrationController;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Controller\OutsetaDemoController;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Controller\TeamController;
use UserFrosting\Sprinkle\Core\Http\Middleware\AuthGuard;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Controller\WebhookController;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Services\UserProvisioner;

class Routes implements \UserFrosting\Routes\RouteDefinitionInterface
{
    public function register(App $app): void
    {
        $app->get('/uf-outseta-integration', OutsetaIntegrationController::class);
        $app->get('/uf-outseta-demo', [OutsetaDemoController::class, 'page']);

        $app->group('/api/outseta/webhooks', function ($app) {
            $app->post('/accountCreated', [WebhookController::class, 'processAccountCreated']);
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
