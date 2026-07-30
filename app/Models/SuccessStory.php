<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuccessStory extends Model
{
    use HasFactory;

    protected $table = 'success_stories';

    protected $fillable = [
        'user_id',
        'couple_name',
        'engagement_date',
        'marriage_date',
        'story',
        'photo',
        'status'
    ];

    /**
     * success_stories has created_at but no updated_at.
     */
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'engagement_date' => 'date',
            'marriage_date' => 'date',
        ];
    }

    /* Relationships */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope approved success stories.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
