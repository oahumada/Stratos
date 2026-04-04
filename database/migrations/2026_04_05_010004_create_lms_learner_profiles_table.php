<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_learner_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('proficiency_level')->default('beginner');
            $table->string('learning_pace')->default('normal');
            $table->string('preferred_content_type')->nullable();
            $table->jsonb('strengths')->nullable();
            $table->jsonb('weaknesses')->nullable();
            $table->unsignedInteger('completed_assessments')->default(0);
            $table->decimal('average_score', 5, 2)->nullable();
            $table->dateTime('last_calibrated_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_learner_profiles');
    }
};
