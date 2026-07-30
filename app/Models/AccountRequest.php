<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountRequest extends Model
{
    use HasFactory;

    protected $table = 'account_requests';

    protected $fillable = [
        'user_id',
        'request_type',
        'reason',
        'status'
    ];

    /**
     * account_requests has created_at and updated_at.
     */
    public $timestamps = true;

    /* Relationships */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
