<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\ServicesProvider;

use Psr\Container\ContainerInterface;
use UserFrosting\ServicesProvider\ServicesProviderInterface;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Services\OutsetaService;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Services\UserProvisioner;

class OutsetaServiceProvider implements ServicesProviderInterface
{
    public function register(): array
    {
        return [
            /**
             * Outseta API service.
             *
             * @return OutsetaService
             */
            OutsetaService::class => function (ContainerInterface $c) {
                return new OutsetaService(
                    $c->get('config'),
                    new \GuzzleHttp\Client(),
                    new UserProvisioner()
                );
            }
        ];
    }
}