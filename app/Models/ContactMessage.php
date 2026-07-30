<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'subject',
        'message',
        'status',
        'reply_text'
    ];

    /**
     * contact_messages has created_at but no updated_at.
     */
    const UPDATED_AT = null;
}
