<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_adaptive_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->string('rule_name');
            $table->string('condition_type');
            $table->string('condition_value');
            $table->string('action_type');
            $table->jsonb('action_config')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_adaptive_rules');
    }
};
