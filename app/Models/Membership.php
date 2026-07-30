<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $table = 'memberships';

    protected $fillable = [
        'plan_name',
        'price',
        'duration_days',
        'contact_limit',
        'featured_profile',
        'priority_support',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_days' => 'integer',
            'contact_limit' => 'integer',
            'featured_profile' => 'boolean',
            'priority_support' => 'boolean',
            'status' => 'boolean'
        ];
    }

    /* Relationships */
    public function userMemberships()
    {
        return $this->hasMany(UserMembership::class, 'membership_id');
    }
}
