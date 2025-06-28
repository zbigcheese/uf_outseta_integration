<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration;

use UserFrosting\Sprinkle\Core\Core;
use UserFrosting\Sprinkle\SprinkleRecipe;

class UfOutsetaIntegration implements SprinkleRecipe
{
    public function getBootstrapper(): string
    {
        return \Zbigcheese\Sprinkles\UfOutsetaIntegration\Sprinkle\UfOutsetaIntegrationBootstrapper::class;
    }
}