<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationField extends Model
{
    use HasFactory;

    protected $table = 'registration_fields';

    protected $fillable = [
        'field_group',
        'field_key',
        'field_label',
        'field_type',
        'field_options',
        'is_custom',
        'is_visible',
        'is_required',
        'is_core',
        'sort_order'
    ];

    /**
     * Disable standard Laravel timestamps if the existing table lacks them.
     * Note: registration_fields table has created_at but no updated_at in database.sql.
     */
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'is_custom' => 'boolean',
            'is_visible' => 'boolean',
            'is_required' => 'boolean',
            'is_core' => 'boolean',
            'sort_order' => 'integer'
        ];
    }

    /* Relationships */
    public function customData()
    {
        return $this->hasMany(UserCustomData::class, 'field_id');
    }

    /**
     * Helper to get options as an array.
     */
    public function getOptionsArrayAttribute(): array
    {
        if (empty($this->field_options)) {
            return [];
        }
        return array_map('trim', explode(',', $this->field_options));
    }
}
