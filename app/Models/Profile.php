<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_FETCHING = 'fetching';
    const STATUS_FETCHED = 'fetched';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'username',
        'status',
        'followers_count',
        'following_count',
        'posts_count',
        'bio',
        'profile_picture_url',
        'last_error',
        'last_refreshed_at',
        'attempts',
        'last_attempt_at',
    ];

    public function snapshots(): HasMany
    {
        return $this->hasMany(ProfileSnapshot::class);
    }
}
