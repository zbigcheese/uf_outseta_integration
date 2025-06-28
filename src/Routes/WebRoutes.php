<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Routes;

use UserFrosting\Routes\RouteDefinitionInterface;
use UserFrosting\Routes\RouteDefinitionTrait;

class WebRoutes implements RouteDefinitionInterface
{
    use RouteDefinitionTrait;

    public function getRouteDefinition(): string
    {
        return __DIR__ . '/../../routes/web.php';
    }
}