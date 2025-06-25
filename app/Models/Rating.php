<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'review',
        'status', // for moderation: pending, approved, rejected
        'reported', // for reporting: boolean
        'report_reason', // for reporting: nullable string
        'admin_note', // for moderation: nullable text
    ];

    protected $casts = [
        'reported' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
} 