<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration;

use UserFrosting\Sprinkle\Core\Core;
use UserFrosting\Sprinkle\Sprinkle;

class UfOutsetaIntegration implements Sprinkle
{
    public function getBootstrapper(): string
    {
        return \Zbigcheese\Sprinkles\UfOutsetaIntegration\Sprinkle\UfOutsetaIntegrationBootstrapper::class;
    }
}