<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_cohort_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained('lms_cohorts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role')->default('member');
            $table->dateTime('joined_at');
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['cohort_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_cohort_members');
    }
};
