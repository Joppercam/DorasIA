<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileFollower extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'follower_id',
    ];

    /**
     * Get the profile being followed.
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the follower profile.
     */
    public function follower()
    {
        return $this->belongsTo(Profile::class, 'follower_id');
    }
}