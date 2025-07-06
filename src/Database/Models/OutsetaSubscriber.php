<?php

namespace UserFrosting\Sprinkle\UfOutsetaIntegration\Database\Models;

use UserFrosting\Sprinkle\Core\Database\Models\Model;
use UserFrosting\Sprinkle\Account\Database\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutsetaSubscriber extends Model
{
    /**
     * The name of the table for this model.
     *
     * @var string
     */
    protected $table = 'outseta_subscribers';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'outseta_uid',
    ];

    /**
     * Get the user this Outseta record belongs to.
     * Defines an inverse one-to-one relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}