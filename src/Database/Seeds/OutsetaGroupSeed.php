<?php

declare(strict_types=1);

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Database\Seeds;

use Illuminate\Support\Str; // <-- Add this use statement
use UserFrosting\Sprinkle\Account\Database\Models\Group;
use UserFrosting\Sprinkle\Account\Database\Models\Role;
use UserFrosting\Sprinkle\Core\Seeder\SeedInterface;

class OutsetaGroupSeed implements SeedInterface
{
    public function run(): void
    {
        // Define the groups and roles we want to create
        $groupsAndRoles = [
            'Outseta Account Owners' => 'A group for users who own an Outseta account subscription.',
            'Outseta Team Accounts'  => 'A group for teammates added by an account owner.',
        ];

        foreach ($groupsAndRoles as $name => $description) {
            // Create the Role if it doesn't exist
            if (Role::where('slug', Str::slug($name))->doesntExist()) {
                $role = new Role([
                    'name'        => $name,
                    'slug'        => Str::slug($name),
                    'description' => $description,
                ]);
                $role->save();
            }

            // Create the Group if it doesn't exist
            if (Group::where('slug', Str::slug($name))->doesntExist()) {
                $group = new Group([
                    'name'        => $name,
                    'slug'        => Str::slug($name),
                    'description' => $description,
                ]);
                $group->save();
            }
        }
    }
}