<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMembership extends Model
{
    use HasFactory;

    protected $table = 'user_memberships';

    protected $fillable = [
        'user_id',
        'membership_id',
        'start_date',
        'end_date',
        'status',
        'can_view_contacts'
    ];

    /**
     * user_memberships has created_at but no updated_at in database.sql.
     */
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'can_view_contacts' => 'boolean'
        ];
    }

    /* Relationships */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }

    /**
     * Scope to check if membership is currently active.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('end_date', '>=', now()->toDateString());
    }
}
