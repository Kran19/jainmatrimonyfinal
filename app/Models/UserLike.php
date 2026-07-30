<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLike extends Model
{
    use HasFactory;

    protected $table = 'user_likes';

    protected $fillable = [
        'user_id',
        'liked_user_id'
    ];

    /**
     * user_likes has created_at but no updated_at.
     */
    const UPDATED_AT = null;

    /* Relationships */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likedUser()
    {
        return $this->belongsTo(User::class, 'liked_user_id');
    }
}
