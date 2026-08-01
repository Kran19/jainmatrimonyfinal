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

    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'status' => 'boolean'
        ];
    }
}
