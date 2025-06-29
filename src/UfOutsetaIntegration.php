<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration;

use UserFrosting\Sprinkle\Core\Bakery\MigrationRecipe;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Database\Migrations\AddOutsetaSubscribersTable;

use UserFrosting\Sprinkle\Core\Core;
use UserFrosting\Sprinkle\SprinkleRecipe;

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
        return [
            Core::class,
        ];
    }

    public function getRoutes(): array
    {
        return [
            Routes::class,
        ];
    }

    /*public function getTemplates(): array
    {
        return [
            'default' => DefaultTemplate::class,
        ];
    }*/

    /**
     * Returns a list of all PHP-DI services/container definitions class.
     *
     * @return class-string<\UserFrosting\ServicesProvider\ServicesProviderInterface>[]
     */
    public function getServices(): array
    {
        // This sprinkle does not have any custom services.
        return [];
    }

    /**
     * Returns a list of all migrations classes for this Sprinkle.
     * This method is required by the MigrationRecipe interface.
     *
     * @return array<class-string<\UserFrosting\Sprinkle\Core\Database\Migration>>
     */
    public function getMigrations(): array
    {
        return [
            AddOutsetaSubscribersTable::class,
        ];
    }
}