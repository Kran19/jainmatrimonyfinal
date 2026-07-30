<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCustomData extends Model
{
    use HasFactory;

    protected $table = 'user_custom_data';

    protected $fillable = [
        'user_id',
        'field_id',
        'field_value'
    ];

    /**
     * user_custom_data has created_at and updated_at in database.sql.
     */
    public $timestamps = true;

    /* Relationships */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function field()
    {
        return $this->belongsTo(RegistrationField::class, 'field_id');
    }
}
