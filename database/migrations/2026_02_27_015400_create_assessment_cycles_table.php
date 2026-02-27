<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            
            // Basic Info
            $table->string('name'); // e.g., "Ciclo Anual 2026", "Evaluación Q1"
            $table->text('description')->nullable();
            
            // Scheduling Mode
            $table->enum('mode', ['specific_date', 'quarterly', 'annual', 'continuous'])->default('specific_date');
            
            // JSON Configurations (To hold complex Wizard data)
            // { "date": "2026-03-15", "quarter": 1, "year": 2026 }
            $table->json('schedule_config')->nullable();
            
            // { "type": "all" | "department" | "scenario", "ids": [1, 2, 3] }
            $table->json('scope')->nullable();
            
            // { "self": true, "manager": true, "peers": 3, "reports": true, "ai": true }
            $table->json('evaluators')->nullable();
            
            // ["disc", "bars", "pulse", "interview"]
            $table->json('instruments')->nullable();
            
            // { "email": true, "slack": false, "dashboard": true }
            $table->json('notifications')->nullable();
            
            // Lifecycle
            $table->enum('status', ['draft', 'scheduled', 'active', 'completed', 'cancelled'])->default('draft');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            
            // Audit trails
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_cycles');
    }
};
