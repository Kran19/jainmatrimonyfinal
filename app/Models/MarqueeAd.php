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
        'status'
    ];

    /**
     * marquee_ads has created_at but no updated_at.
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
