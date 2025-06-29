<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Services;

use UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface;
use UserFrosting\Sprinkle\Account\Database\Models\User;
use UserFrosting\Sprinkle\Core\Facades\Config;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Database\Models\OutsetaSubscriber;

class UserProvisioner
{
    /**
     * Finds a local user by their Outseta UID or creates one based on Outseta profile data.
     *
     * @param array $outsetaPerson The user data array from the Outseta API.
     * @return UserInterface The found or newly created UserFrosting user.
     */
    public function findOrCreate(array $outsetaPerson): UserInterface
    {
        // First, try to find an existing user via the relationship.
        $subscriber = OutsetaSubscriber::where('outseta_uid', $outsetaPerson['Uid'])->first();

        if ($subscriber) {
            return $subscriber->user; // Eloquent relationships make this easy!
        }

        // If not found, try to find an unlinked user by email.
        $user = User::where('email', $outsetaPerson['Email'])->first();

        // If user doesn't exist at all, create a new one.
        if (!$user) {
            $defaultLocale = Config::getString('site.registration.user_defaults.locale', 'en_US');
            $defaultGroupId = Config::getInt('site.registration.user_defaults.group_id', 1);

            $user = new User([
                'user_name'     => $outsetaPerson['Email'],
                'first_name'    => $outsetaPerson['FirstName'],
                'last_name'     => $outsetaPerson['LastName'],
                'email'         => $outsetaPerson['Email'],
                'locale'        => $defaultLocale,
                'group_id'      => $defaultGroupId,
                'flag_verified' => 1,
                'password'      => bin2hex(random_bytes(16))
            ]);
            $user->save();
        }

        // Now that we have a user (either found by email or newly created),
        // create the linking OutsetaSubscriber record for them.
        OutsetaSubscriber::create([
            'user_id'     => $user->id,
            'outseta_uid' => $outsetaPerson['Uid'],
        ]);
        
        // Return the main user object
        return $user;
    }
}