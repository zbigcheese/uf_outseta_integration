<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration;

//use UserFrosting\Sprinkle\Sprinkle;

class UfOutsetaIntegration implements \UserFrosting\Sprinkle\Sprinkle
{
    public function getBootstrapper(): string
    {
        return \Zbigcheese\Sprinkles\UfOutsetaIntegration\Sprinkle\UfOutsetaIntegrationBootstrapper::class;
    }
}