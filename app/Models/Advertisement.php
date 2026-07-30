<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $table = 'advertisements';

    /**
     * Disable automatic timestamp updating to support legacy schema without updated_at column.
     */
    public $timestamps = false;

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
