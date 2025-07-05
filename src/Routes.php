<?php

declare(strict_types=1);

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration;

//use Psr\Http\Message\ResponseInterface as Response;
//use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller\OutsetaIntegrationController;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller\OutsetaDemoController;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller\TeamController;
use UserFrosting\Sprinkle\Core\Http\Middleware\AuthGuard;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller\WebhookController;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Services\UserProvisioner;

class Routes implements \UserFrosting\Routes\RouteDefinitionInterface
{
    public function register(App $app): void
    {
        $app->get('/uf-outseta-integration', OutsetaIntegrationController::class);
        $app->get('/uf-outseta-demo', [OutsetaDemoController::class, 'page']);

        $app->group('/api/outseta/webhooks', function ($app) {
            $app->post('', [WebhookController::class, 'processAccountCreated']);
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
