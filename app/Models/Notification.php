<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Get the time ago for the notification
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the icon based on notification type
     */
    public function getIconAttribute()
    {
        return match ($this->type) {
            'user_registered' => 'fa-user-plus',
            'book_added' => 'fa-book',
            'user_verified' => 'fa-user-check',
            'system' => 'fa-cog',
            'download' => 'fa-download',
            'rating' => 'fa-star',
            default => 'fa-bell',
        };
    }

    /**
     * Get the color class based on notification type
     */
    public function getColorClassAttribute()
    {
        return match ($this->type) {
            'user_registered' => 'text-primary',
            'book_added' => 'text-success',
            'user_verified' => 'text-info',
            'system' => 'text-warning',
            'download' => 'text-secondary',
            'rating' => 'text-warning',
            default => 'text-muted',
        };
    }
}
