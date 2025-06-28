<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Sprinkle;

use UserFrosting\Sprinkle\Bootstrapper;

class UfOutsetaIntegrationBootstrapper extends Bootstrapper
{
    public function getRoutes(): array
    {
        return [
            \Zbigcheese\Sprinkles\UfOutsetaIntegration\Routes\WebRoutes::class,
        ];
    }

    public function getTemplates(): array
    {
        return [
            'default' => \Zbigcheese\Sprinkles\UfOutsetaIntegration\Templates\DefaultTemplate::class,
        ];
    }
}