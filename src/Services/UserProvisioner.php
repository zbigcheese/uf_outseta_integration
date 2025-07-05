<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Services;

use UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface;
use UserFrosting\Sprinkle\Account\Database\Models\User;
use UserFrosting\Sprinkle\Core\Facades\Config;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Database\Models\OutsetaSubscriber;

class UserProvisioner
{
    /**
     * Finds a local user or creates one, assigning them to a specific group.
     *
     * @param array $outsetaPerson The user data array from the Outseta API.
     * @param string $groupSlug The slug of the group to assign the new user to.
     * @return UserInterface The found or newly created UserFrosting user.
     */
    public function findOrCreate(array $outsetaPerson, string $groupSlug): UserInterface
    {
        $subscriber = OutsetaSubscriber::where('outseta_uid', $outsetaPerson['Uid'])->first();
        if ($subscriber) {
            return $subscriber->user;
        }

        $user = User::where('email', $outsetaPerson['Email'])->first();

        // If user doesn't exist, create them.
        if (!$user) {
            // Find the group by its slug
            $group = Group::where('slug', $groupSlug)->first();
            $groupId = $group ? $group->id : Config::getInt('site.registration.user_defaults.group_id', 1);

            $user = new User([
                'user_name'     => $outsetaPerson['Email'],
                'first_name'    => $outsetaPerson['FirstName'],
                'last_name'     => $outsetaPerson['LastName'],
                'email'         => $outsetaPerson['Email'],
                'locale'        => Config::getString('site.registration.user_defaults.locale', 'en_US'),
                'group_id'      => $groupId,
                'flag_verified' => 1,
                'password'      => bin2hex(random_bytes(16))
            ]);
            $user->save();
        }

        // Create the linking OutsetaSubscriber record.
        OutsetaSubscriber::create([
            'user_id'     => $user->id,
            'outseta_uid' => $outsetaPerson['Uid'],
        ]);
        
        return $user;
    }
}