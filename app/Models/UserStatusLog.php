<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatusLog extends Model
{
    protected $table = 'profile_status_logs';

    protected $fillable = [
        'user_id',
        'status',
        'reason',
        'performed_by',
        'performed_by_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
