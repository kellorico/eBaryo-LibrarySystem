<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Create a new notification
     */
    public static function create($type, $title, $message, $data = null)
    {
        return Notification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Create user registration notification
     */
    public static function userRegistered($user)
    {
        return self::create(
            'user_registered',
            'New User Registration',
            "New user '{$user->name}' has registered and is waiting for verification.",
            ['user_id' => $user->id, 'user_name' => $user->name, 'user_email' => $user->email]
        );
    }

    /**
     * Create book added notification
     */
    public static function bookAdded($book)
    {
        return self::create(
            'book_added',
            'New Book Added',
            "New book '{$book->title}' has been added to the library.",
            ['book_id' => $book->id, 'book_title' => $book->title, 'category' => $book->category->name]
        );
    }

    /**
     * Create user verification notification
     */
    public static function userVerified($user)
    {
        return self::create(
            'user_verified',
            'User Verified',
            "User '{$user->name}' has been verified successfully.",
            ['user_id' => $user->id, 'user_name' => $user->name]
        );
    }

    /**
     * Create book download notification
     */
    public static function bookDownloaded($user, $book)
    {
        return self::create(
            'download',
            'Book Downloaded',
            "User '{$user->name}' downloaded '{$book->title}'.",
            ['user_id' => $user->id, 'user_name' => $user->name, 'book_id' => $book->id, 'book_title' => $book->title]
        );
    }

    /**
     * Create book rating notification
     */
    public static function bookRated($user, $book, $rating)
    {
        return self::create(
            'rating',
            'Book Rated',
            "User '{$user->name}' rated '{$book->title}' with {$rating} stars.",
            ['user_id' => $user->id, 'user_name' => $user->name, 'book_id' => $book->id, 'book_title' => $book->title, 'rating' => $rating]
        );
    }

    /**
     * Create system notification
     */
    public static function system($title, $message, $data = null)
    {
        return self::create('system', $title, $message, $data);
    }

    /**
     * Create custom notification
     */
    public static function custom($type, $title, $message, $data = null)
    {
        return self::create($type, $title, $message, $data);
    }

    /**
     * Notify user their review was approved
     */
    public static function reviewApproved($user, $book)
    {
        return self::create(
            'review_approved',
            'Review Approved',
            "Your review for '{$book->title}' was approved.",
            ['user_id' => $user->id, 'book_id' => $book->id, 'book_title' => $book->title]
        );
    }

    /**
     * Notify user their review was rejected
     */
    public static function reviewRejected($user, $book)
    {
        return self::create(
            'review_rejected',
            'Review Rejected',
            "Your review for '{$book->title}' was rejected.",
            ['user_id' => $user->id, 'book_id' => $book->id, 'book_title' => $book->title]
        );
    }

    /**
     * Notify admins a review was reported
     */
    public static function reviewReported($review)
    {
        // Notify all admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            self::create(
                'review_reported',
                'Review Reported',
                "A review for '{$review->book->title}' was reported.",
                [
                    'review_id' => $review->id,
                    'user_id' => $review->user_id,
                    'book_id' => $review->book_id,
                    'reason' => $review->report_reason,
                ]
            );
        }
    }
}
