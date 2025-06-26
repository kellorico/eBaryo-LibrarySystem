<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'suggestion',
        'status',
        'admin_response',
        'responded_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
