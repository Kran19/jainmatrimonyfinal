<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SarankshakMember extends Model
{
    use HasFactory;

    protected $table = 'sarankshak_members';

    protected $fillable = [
        'name',
        'name_en',
        'designation',
        'designation_en',
        'description',
        'description_en',
        'photo',
        'sort_order',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'sort_order' => 'integer'
        ];
    }
}