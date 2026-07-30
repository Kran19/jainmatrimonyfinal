<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarqueeAd extends Model
{
    use HasFactory;

    protected $table = 'marquee_ads';

    protected $fillable = [
        'notice_text',
        'advertisement_text',
        'status'
    ];

    /**
     * Get notice text accessor (supports both notice_text and advertisement_text columns).
     */
    public function getNoticeTextAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        return $this->attributes['advertisement_text'] ?? null;
    }

    /**
     * marquee_ads created_at handling.
     */
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'status' => 'boolean'
        ];
    }

    /**
     * Scope active marquees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
