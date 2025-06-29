<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionUpvote extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'suggestion_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function suggestion()
    {
        return $this->belongsTo(Suggestion::class);
    }
} 