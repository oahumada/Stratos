<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('succession_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('person_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('target_role_id')->constrained('roles')->onDelete('cascade');
            $table->decimal('readiness_score', 5, 2);
            $table->string('readiness_level');
            $table->integer('estimated_months')->nullable();
            $table->enum('status', ['draft', 'active', 'implemented', 'archived'])->default('draft');
            $table->json('metrics')->nullable();
            $table->json('gaps')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('succession_plans');
    }
};
