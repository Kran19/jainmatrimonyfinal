<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRelative extends Model
{
    use HasFactory;

    protected $table = 'user_relatives';

    protected $fillable = [
        'user_id',
        'relation',
        'name',
        'mobile',
        'occupation'
    ];

    /**
     * user_relatives has created_at but no updated_at.
     */
    const UPDATED_AT = null;

    /* Relationships */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
