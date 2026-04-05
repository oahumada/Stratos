<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_communities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('practice');
            $table->string('practice_domain')->nullable();
            $table->json('domain_skills')->nullable();
            $table->json('learning_goals')->nullable();
            $table->string('status')->default('active');
            $table->integer('max_members')->nullable();
            $table->decimal('health_score', 5, 2)->default(0);
            $table->decimal('social_presence', 5, 2)->default(0);
            $table->decimal('cognitive_presence', 5, 2)->default(0);
            $table->decimal('teaching_presence', 5, 2)->default(0);
            $table->foreignId('facilitator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('lms_courses')->nullOnDelete();
            $table->string('image_url')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });

        Schema::create('lms_community_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained('lms_communities')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role')->default('novice');
            $table->string('lpp_stage')->default('peripheral');
            $table->decimal('contribution_score', 8, 2)->default(0);
            $table->integer('discussions_count')->default(0);
            $table->integer('ugc_count')->default(0);
            $table->integer('peer_reviews_count')->default(0);
            $table->integer('mentorships_count')->default(0);
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->unique(['community_id', 'user_id']);
            $table->index(['community_id', 'role']);
        });

        Schema::create('lms_community_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained('lms_communities')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('activity_type');
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->json('metadata')->nullable();
            $table->string('presence_type')->nullable();
            $table->integer('engagement_score')->default(0);
            $table->timestamps();

            $table->index(['community_id', 'activity_type']);
            $table->index(['community_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_community_activities');
        Schema::dropIfExists('lms_community_members');
        Schema::dropIfExists('lms_communities');
    }
};
