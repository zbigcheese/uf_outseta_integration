<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration;

// Corrected 'use' statements
use UserFrosting\Sprinkle\SprinkleRecipe;
use UserFrosting\Sprinkle\Core\Sprinkle\Recipe\MigrationRecipe;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Database\Migrations\AddOutsetaSubscribersTable;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\ServicesProvider\OutsetaServiceProvider;

class UfOutsetaIntegration implements SprinkleRecipe, MigrationRecipe
{
    public function getName(): string
    {
        return 'Outseta Integration';
    }

    public function getPath(): string
    {
        return __DIR__ . '/../';
    }

    public function getServices(): array
    {
        return [
            OutsetaServiceProvider::class,
        ];
    }

    public function getSprinkles(): array
    {
        return [];
    }

    public function getRoutes(): array
    {
        return [
            Routes::class,
        ];
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

    /**
     * Returns a list of all Bakery commands for this Sprinkle.
     *
     * @return array<class-string<\Symfony\Component\Console\Command\Command>>
     */
    public function getBakeryCommands(): array
    {
        // We can leave this in for future use or remove if you prefer.
        // return [
        //     ForceMigrateCommand::class,
        // ];
        return [];
    }
}