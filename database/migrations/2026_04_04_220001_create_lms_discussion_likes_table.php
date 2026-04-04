<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_discussion_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_discussion_id')->constrained('lms_discussions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['lms_discussion_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_discussion_likes');
    }
};
