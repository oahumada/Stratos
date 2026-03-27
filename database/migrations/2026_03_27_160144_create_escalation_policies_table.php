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
        Schema::create('escalation_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->enum('severity', ['critical', 'high', 'medium', 'low']);
            $table->json('recipients'); // array of email/user_ids
            $table->enum('notification_type', ['email', 'slack', 'both'])->default('email');
            $table->integer('delay_minutes')->default(0); // delay before escalation
            $table->boolean('is_active')->default(true);
            $table->integer('escalation_level')->default(1); // 1st, 2nd, 3rd level
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['organization_id', 'severity', 'escalation_level'], 'unique_org_severity_level');
            $table->index(['organization_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalation_policies');
    }
};
