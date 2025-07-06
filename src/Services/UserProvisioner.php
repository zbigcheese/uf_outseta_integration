<?php

namespace UserFrosting\Sprinkle\UfOutsetaIntegration\Services;

use UserFrosting\Config\Config;
use UserFrosting\Sprinkle\Account\Database\Models\Group;
use UserFrosting\Sprinkle\Account\Database\Models\Role;
use UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface;
use UserFrosting\Sprinkle\Account\Database\Models\User;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Database\Models\OutsetaSubscriber;


class UserProvisioner
{
    public function __construct(protected Config $config)
{
}

    public function findOrCreate(array $outsetaPerson, string $groupSlug): UserInterface
    {
        $subscriber = OutsetaSubscriber::where('outseta_uid', $outsetaPerson['Uid'])->first();
        if ($subscriber) {
            return $subscriber->user;
        }

        $user = User::where('email', $outsetaPerson['Email'])->first();

        if (!$user) {
            $group = Group::where('slug', $groupSlug)->first();
            $groupId = $group ? $group->id : $this->config->getInt('site.registration.user_defaults.group_id', 1);

            $user = new User([
                'user_name'     => $outsetaPerson['Email'],
                'first_name'    => $outsetaPerson['FirstName'],
                'last_name'     => $outsetaPerson['LastName'],
                'email'         => $outsetaPerson['Email'],
                'locale'        => $this->config->getString('site.registration.user_defaults.locale', 'en_US'),
                'group_id'      => $groupId,
                'flag_verified' => 1,
                'password'      => bin2hex(random_bytes(16))
            ]);
            $user->save();

            // If this is a new owner, find and attach the corresponding role.
            if ($groupSlug === 'outseta-account-owners') {
                $ownerRole = Role::where('slug', 'outseta-account-owners')->first();
                if ($ownerRole) {
                    $user->roles()->attach($ownerRole->id);
                }
            }
        }

        OutsetaSubscriber::create([
            'user_id'     => $user->id,
            'outseta_uid' => $outsetaPerson['Uid'],
        ]);
        
        return $user;
    }
}
