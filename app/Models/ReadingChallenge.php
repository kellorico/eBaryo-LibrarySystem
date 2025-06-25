<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingChallenge extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'target_books',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'challenge_user', 'challenge_id', 'user_id')
            ->withPivot('progress', 'completed_at')
            ->withTimestamps();
    }
}
