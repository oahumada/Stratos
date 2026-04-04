<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->jsonb('questions'); // [{type, question, options}]
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_surveys');
    }
};
