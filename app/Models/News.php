<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $fillable = [
        'title',
        'content',
        'image',
        'status'
    ];

    /**
     * news has created_at but no updated_at.
     */
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'status' => 'boolean'
        ];
    }
}
