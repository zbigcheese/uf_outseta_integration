<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\ServicesProvider;

use Psr\Container\ContainerInterface;
use UserFrosting\ServicesProvider\ServicesProviderInterface;
// Import the necessary classes
use UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Database\Models\User as ExtendedUser;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Services\OutsetaService;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Services\UserProvisioner;

class OutsetaServiceProvider implements ServicesProviderInterface
{
    public function register(): array
    {
        return [
            // ... Other services might be here ...

            OutsetaService::class => function (ContainerInterface $c) {
                // Just pass the container itself to the constructor
                return new OutsetaService($c);
            },

            UserProvisioner::class => function (ContainerInterface $c) {
                return new UserProvisioner();
            },
            
            UserInterface::class => function (ContainerInterface $c) {
                return new ExtendedUser();
            },
        ];
    }
}