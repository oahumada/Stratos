<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_learning_path_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_learning_path_id')->constrained('lms_learning_paths')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('status')->default('active');
            $table->float('progress_percentage')->default(0);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['lms_learning_path_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_learning_path_enrollments');
    }
};
