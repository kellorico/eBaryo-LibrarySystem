<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Alice Verified',
            'email' => 'alice@example.com',
            'email_verified_at' => now(),
        ]);
        User::factory()->create([
            'name' => 'Bob Verified',
            'email' => 'bob@example.com',
            'email_verified_at' => now(),
        ]);
        User::factory()->create([
            'name' => 'Charlie Verified',
            'email' => 'charlie@example.com',
            'email_verified_at' => now(),
        ]);

        // Seed notifications for testing
        $this->call([
            NotificationSeeder::class,
        ]);

        // Seed a review for moderation testing
        \App\Models\Rating::create([
            'user_id' => 1, // adjust as needed
            'book_id' => 1, // adjust as needed
            'rating' => 3,
            'review' => 'This book contains some questionable content.',
            'status' => 'pending',
            'reported' => true,
            'report_reason' => 'Inappropriate language',
        ]);
    }
}
