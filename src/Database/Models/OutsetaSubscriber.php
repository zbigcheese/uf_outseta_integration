<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Database\Models;

use UserFrosting\Sprinkle\Core\Database\Models\Model;

class OutsetaSubscriber extends Model
{
    protected $table = 'outseta_subscribers';

    protected $fillable = [
        'user_id',
        'outseta_uid',
    ];
}