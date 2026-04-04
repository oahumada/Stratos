<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_learning_path_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_learning_path_id')->constrained('lms_learning_paths')->cascadeOnDelete();
            $table->foreignId('lms_course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->foreignId('unlock_after_item_id')->nullable()->constrained('lms_learning_path_items')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_learning_path_items');
    }
};
