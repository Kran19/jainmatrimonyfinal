<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'gallery';

    protected $fillable = [
        'title',
        'category',
        'image_path',
        'media_type',
        'media_url',
        'status'
    ];

    /**
     * gallery has created_at but no updated_at.
     */
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'status' => 'boolean'
        ];
    }
}
