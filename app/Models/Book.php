<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;
    
    protected $fillable = [
        'title',
        'author',
        'description',
        'file_path',
        'cover_image',
        'published_year',
        'category_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }
}
