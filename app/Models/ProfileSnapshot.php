<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileSnapshot extends Model
{
    protected $fillable = [
        'profile_id',
        'followers_count',
        'following_count',
        'posts_count',
        'captured_at',
    ];

    public function profile()
    {
        return $this->belongsTo(
            Profile::class
        );
    }
}
