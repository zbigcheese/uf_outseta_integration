<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Database\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use UserFrosting\Sprinkle\Account\Database\Models\User as CoreUser;

/**
 * User Class
 *
 * Represents a User object, extended for Outseta integration.
 * @property OutsetaSubscriber|null $outsetaSubscriber The subscriber details from Outseta.
 */
class User extends CoreUser
{
    /**
     * Define a one-to-one relationship to the OutsetaSubscriber model.
     * This means every User can have one associated Outseta record.
     */
    public function outsetaSubscriber(): HasOne
    {
        return $this->hasOne(OutsetaSubscriber::class, 'user_id');
    }
}