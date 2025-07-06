<?php

namespace UserFrosting\Sprinkle\UfOutsetaIntegration\ServicesProvider;

// Add these new 'use' statements
use Psr\Container\ContainerInterface;
use Slim\App;
use UserFrosting\Sprinkle\Account\Authenticate\Authenticator;
use UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager;

use UserFrosting\ServicesProvider\ServicesProviderInterface;
use UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Database\Models\User as ExtendedUser;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Http\Middleware\SubscriptionAuthMiddleware;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Services\OutsetaService;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Services\UserProvisioner;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Controller\WebhookController;

class OutsetaServiceProvider implements ServicesProviderInterface
{
    public function register(): array
    {
        return [
            // This service registration is fine, we'll use autowire.
            OutsetaService::class => \DI\autowire(OutsetaService::class),

            // This one is also fine.
            UserProvisioner::class => \DI\autowire(UserProvisioner::class),

            // This one is also fine.
            UserInterface::class => \DI\create(ExtendedUser::class),

            WebhookController::class => \DI\autowire(WebhookController::class),
            
            // --- THIS IS THE FIX ---
            // We replace the autowiring for the middleware with a manual factory.
            SubscriptionAuthMiddleware::class => function (ContainerInterface $c) {
                $app = $c->get(App::class);

                return new SubscriptionAuthMiddleware(
                    $c->get(Authenticator::class),
                    $c->get(AuthorizationManager::class), // <-- Add the new dependency here
                    $c->get(OutsetaService::class),
                    $app->getResponseFactory()
                );
            },
        ];
    }
}