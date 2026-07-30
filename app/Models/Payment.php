<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'membership_id',
        'amount',
        'transaction_id',
        'payment_method',
        'payment_remarks',
        'payment_screenshot',
        'status',
        'verified_by',
        
        // Backup columns at registration time
        'full_name',
        'phone_number',
        'email',
        'address',
        'dob'
    ];

    /**
     * Disable updated_at as it is not defined in the database.sql schema for payments.
     */
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'dob' => 'date',
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

    public function verifier()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }
}
