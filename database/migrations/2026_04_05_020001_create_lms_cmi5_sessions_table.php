<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_cmi5_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('lms_scorm_packages')->onDelete('cascade');
            $table->uuid('registration_id');
            $table->uuid('session_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('actor_json');
            $table->string('launch_mode')->default('Normal');
            $table->string('launch_url');
            $table->string('return_url')->nullable();
            $table->string('move_on')->default('NotApplicable');
            $table->decimal('mastery_score', 5, 4)->nullable();
            $table->string('status')->default('launched');
            $table->boolean('satisfied')->default(false);
            $table->decimal('score_scaled', 5, 4)->nullable();
            $table->string('duration')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_cmi5_sessions');
    }
};
