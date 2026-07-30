<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $table = 'advertisements';

    protected $fillable = [
        'title',
        'image',
        'link',
        'position',
        'media_type',
        'status'
    ];

    /**
     * advertisements has created_at but no updated_at.
     */
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'status' => 'boolean'
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
