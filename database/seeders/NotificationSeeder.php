<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notifications = [
            [
                'type' => 'user_registered',
                'title' => 'New User Registration',
                'message' => 'New user "John Doe" has registered and is waiting for verification.',
                'data' => ['user_id' => 1, 'user_name' => 'John Doe', 'user_email' => 'john@example.com'],
                'is_read' => false,
                'created_at' => now()->subMinutes(5),
            ],
            [
                'type' => 'book_added',
                'title' => 'New Book Added',
                'message' => 'New book "The Adventures of Tom Sawyer" has been added to the library.',
                'data' => ['book_id' => 1, 'book_title' => 'The Adventures of Tom Sawyer', 'category' => 'Story Books'],
                'is_read' => false,
                'created_at' => now()->subMinutes(15),
            ],
            [
                'type' => 'user_verified',
                'title' => 'User Verified',
                'message' => 'User "Jane Smith" has been verified successfully.',
                'data' => ['user_id' => 2, 'user_name' => 'Jane Smith'],
                'is_read' => true,
                'read_at' => now()->subMinutes(30),
                'created_at' => now()->subHours(1),
            ],
            [
                'type' => 'download',
                'title' => 'Book Downloaded',
                'message' => 'User "Mike Johnson" downloaded "Mathematics for Beginners".',
                'data' => ['user_id' => 3, 'user_name' => 'Mike Johnson', 'book_id' => 2, 'book_title' => 'Mathematics for Beginners'],
                'is_read' => false,
                'created_at' => now()->subHours(2),
            ],
            [
                'type' => 'rating',
                'title' => 'Book Rated',
                'message' => 'User "Sarah Wilson" rated "Science Experiments" with 5 stars.',
                'data' => ['user_id' => 4, 'user_name' => 'Sarah Wilson', 'book_id' => 3, 'book_title' => 'Science Experiments', 'rating' => 5],
                'is_read' => false,
                'created_at' => now()->subHours(3),
            ],
            [
                'type' => 'system',
                'title' => 'System Maintenance',
                'message' => 'Scheduled system maintenance completed successfully.',
                'data' => ['maintenance_type' => 'routine', 'duration' => '30 minutes'],
                'is_read' => true,
                'read_at' => now()->subHours(4),
                'created_at' => now()->subHours(5),
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }
    }
} 