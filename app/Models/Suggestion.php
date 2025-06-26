<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SuggestionUpvote;

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

    public function upvotes()
    {
        return $this->hasMany(SuggestionUpvote::class);
    }

    public function getUpvotesCountAttribute()
    {
        return $this->upvotes()->count();
    }

    public function hasUpvoted($userId)
    {
        return $this->upvotes()->where('user_id', $userId)->exists();
    }
}
