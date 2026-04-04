<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_peer_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignment_id')->constrained('lms_lessons')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();
            $table->string('submission_url')->nullable();
            $table->text('submission_text')->nullable();
            $table->decimal('review_score', 5, 2)->nullable();
            $table->text('review_feedback')->nullable();
            $table->jsonb('rubric_scores')->nullable();
            $table->string('status')->default('pending_submission');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['assignment_id', 'reviewee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_peer_reviews');
    }
};
