<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('suggestion_upvotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('suggestion_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'suggestion_id']);
        });
    }
    public function down()
    {
        Schema::dropIfExists('suggestion_upvotes');
    }
}; 