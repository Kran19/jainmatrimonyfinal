<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $table = 'advertisements';

    /**
     * Legacy advertisements table has created_at but no updated_at column.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'title',
        'image',
        'link',
        'position',
        'media_type',
        'sort_order',
        'duration_seconds',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'sort_order' => 'integer',
            'duration_seconds' => 'integer',
        ];
    }

    /**
     * Scope active advertisements.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
