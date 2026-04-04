<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_session_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('lms_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('registered'); // registered, confirmed, attended, absent, cancelled
            $table->dateTime('check_in_at')->nullable();
            $table->dateTime('check_out_at')->nullable();
            $table->text('feedback')->nullable();
            $table->tinyInteger('rating')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_session_attendances');
    }
};
