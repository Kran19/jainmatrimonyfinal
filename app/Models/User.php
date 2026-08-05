<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_id',
        'full_name',
        'email',
        'mobile',
        'country_code',
        'password_hash',  // actual DB column used for auth
        'are_you_digambar_jain',
        
        // Cast & Religion
        'cast',
        'subcast',
        'custom_subcast',
        
        // Address
        'permanent_address',
        'pin_code',
        'current_address',
        
        // Family
        'father_name',
        'father_mobile',
        'father_income',
        'father_occupation',
        'mother_name',
        'mother_mobile',
        'mother_occupation',
        'mother_occupation_details',
        'brothers',
        'brothers_married',
        'brothers_unmarried',
        'sisters',
        'sisters_married',
        'sisters_unmarried',

        // Mandir / Community Verification
        'mandir',
        'custom_mandir',
        'mandir_name',
        'mandir_address',
        'mandir_pincode',

        // References
        'ref1_name',
        'ref1_mobile',
        'ref1_relation',
        'ref2_name',
        'ref2_mobile',
        'ref2_relation',

        'filled_by',
        'id_proof_type',
        'id_proof_path',
        
        // Profile details
        'gender',
        'birth_date',
        'birth_time',
        'birth_place',
        'native_place',
        'gotra',
        'mama_gotra',
        'manglik',
        'height',
        'weight',
        'marital_status',
        'handicapped',
        
        // Professional
        'higher_education',
        'occupation',
        'company_name',
        'designation',
        'monthly_income',
        
        // Lifestyle & Preferences
        'languages',
        'hobbies',
        'partner_preference',
        
        // Media & Payments
        'profile_photo',
        'family_photo',
        'profile_photo_drive_url',
        'payment_screenshot',
        'payment_proof_drive_url',
        'payment_transaction_id',
        'payment_status',
        
        // Admin approval statuses
        'status',
        'is_approved',
        'verified',
        'approved_by',
        'approved_at',
        'featured_until',
        'has_set_password',
        'registration_source',
        'is_public',
        'registration_step',
        'registration_count',
        'deletion_count',
        'approval_date',
        'expiry_date',
        'income_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the password for the user (handles legacy column name password_hash).
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Get the name of the password attribute for the user.
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'approved_at' => 'datetime',
            'featured_until' => 'date',
            'verified' => 'boolean',
            'is_public' => 'boolean',
            'is_approved' => 'boolean',
            'has_set_password' => 'boolean',
            'monthly_income' => 'decimal:2',
            'weight' => 'string',
            'registration_count' => 'integer',
            'deletion_count' => 'integer',
        ];
    }

    /* Scopes */
    public function scopeApproved($query)
    {
        $query->where('status', 'approved');
        // Only filter by is_approved if the column exists in the DB
        // (guards against running before the migration has been applied)
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_approved')) {
            $query->where('is_approved', 1);
        }
        return $query;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /* Relationships */
    public function customData()
    {
        return $this->hasMany(UserCustomData::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function memberships()
    {
        return $this->hasMany(UserMembership::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(UserLike::class, 'user_id');
    }

    public function relatives()
    {
        return $this->hasMany(UserRelative::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    /**
     * Determine if candidate has a valid profile photo on disk or as data URI.
     */
    public function hasProfilePhoto(): bool
    {
        if (empty($this->profile_photo)) {
            return false;
        }
        if (str_starts_with($this->profile_photo, 'data:image/')) {
            return true;
        }
        return resolve_media_path($this->profile_photo) !== null;
    }

    /**
     * Get computed profile photo URL or default avatar fallback.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->hasProfilePhoto()) {
            return route('image.serve', ['file' => $this->profile_photo]);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=random';
    }

    /**
     * Always sanitize and parse birth_time before setting attribute on the model.
     * Guarantees 24-hour SQL TIME compatibility ('HH:MM:SS' or null) across all controllers.
     */
    public function setBirthTimeAttribute($value)
    {
        if (empty($value) || trim($value) === '' || strtoupper(trim($value)) === 'N/A') {
            $this->attributes['birth_time'] = null;
            return;
        }

        $timeStr = trim($value);

        // Check if already in 24-hour HH:MM:SS or HH:MM format
        if (preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $timeStr)) {
            $parts = explode(':', $timeStr);
            $h = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
            $m = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
            $s = isset($parts[2]) ? str_pad($parts[2], 2, '0', STR_PAD_LEFT) : '00';
            $this->attributes['birth_time'] = "{$h}:{$m}:{$s}";
            return;
        }

        $timestamp = strtotime($timeStr);
        if ($timestamp !== false) {
            $this->attributes['birth_time'] = date('H:i:s', $timestamp);
            return;
        }

        $this->attributes['birth_time'] = null;
    }
}
