<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration;

use UserFrosting\Sprinkle\SprinkleRecipe;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Routes\WebRoutes;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Templates\DefaultTemplate;

class UfOutsetaIntegration implements SprinkleRecipe
{
    public function getName(): string
    {
        return 'Outseta Integration';
    }

    public function getPath(): string
    {
        return __DIR__ . '/../';
    }

    public function getSprinkles(): array
    {
        // This sprinkle has no dependencies on other sprinkles.
        // Its dependencies on framework/core are handled by composer.json.
        return [];
    }

    public function getRoutes(): array
    {
        return [
            WebRoutes::class,
        ];
    }

    public function getTemplates(): array
    {
        return [
            'default' => DefaultTemplate::class,
        ];
    }
}
