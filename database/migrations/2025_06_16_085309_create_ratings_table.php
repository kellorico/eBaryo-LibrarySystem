<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->default(0); // Rating from 0 to 5
            $table->text('review')->nullable(); // Optional review text
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Moderation status
            $table->boolean('reported')->default(false); // If review is reported
            $table->string('report_reason')->nullable(); // Reason for report
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
