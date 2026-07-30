<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoGallery extends Model
{
    use HasFactory;

    protected $table = 'video_gallery';

    protected $fillable = [
        'title',
        'video_type',
        'video_url',
        'video_file',
        'thumbnail',
        'description',
        'display_order',
        'status'
    ];

    /**
     * video_gallery has created_at and updated_at.
     */
    public $timestamps = true;

    /**
     * Scope active items.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
