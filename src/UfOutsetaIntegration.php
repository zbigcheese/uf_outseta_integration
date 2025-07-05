<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration;

// These 'use' statements declare the dependencies
use UserFrosting\Sprinkle\Core\Core;
use UserFrosting\Sprinkle\Account\Account;
use UserFrosting\Sprinkle\Admin\Admin;

use UserFrosting\Sprinkle\SprinkleRecipe;
use UserFrosting\Sprinkle\Core\Sprinkle\Recipe\MigrationRecipe;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Database\Migrations\AddOutsetaSubscribersTable;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\ServicesProvider\OutsetaServiceProvider;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Routes;

use UserFrosting\Sprinkle\Core\Sprinkle\Recipe\SeedRecipe;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Database\Seeds\OutsetaGroupSeed;

class UfOutsetaIntegration implements SprinkleRecipe, MigrationRecipe, SeedRecipe
{
    public function getName(): string { return 'Outseta Integration'; }

    public function getPath(): string { return __DIR__ . '/../'; }

    public function getServices(): array { return [ OutsetaServiceProvider::class ]; }

    public function getRoutes(): array { return [ Routes::class ]; }

    public function getMigrations(): array { return [ AddOutsetaSubscribersTable::class ]; }

    public function getBakeryCommands(): array { return []; }

    /**
     * Returns a list of Sprinkles this Sprinkle depends on.
     */
    public function getSprinkles(): array
    {
        return [
            Core::class,
            Account::class,
            Admin::class,
        ];
    }

    /**
     * Returns a list of all seeds classes for this Sprinkle.
     *
     * @return array<class-string<SeedInterface>>
     */
    public function getSeeds(): array
    {
        return [
            OutsetaGroupSeed::class,
        ];
    }
}