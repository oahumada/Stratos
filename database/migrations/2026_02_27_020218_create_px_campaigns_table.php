<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('px_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            
            $table->string('name');
            $table->text('description')->nullable();
            
            // Scheduling Mode for PX: fixed dates, recurring, or "agent_autonomous" (Stratos decides when and to whom)
            $table->enum('mode', ['specific_date', 'recurring', 'agent_autonomous'])->default('agent_autonomous');
            
            $table->json('schedule_config')->nullable(); // { "frequency": "monthly", "random_days": true }
            $table->json('scope')->nullable();           // { "type": "all" | "department" | "randomized_sample", "target_pct": 20 }
            
            // The topics the AI should measure: "clima", "health", "stress", "burnout"
            $table->json('topics')->nullable();
            
            $table->enum('status', ['draft', 'scheduled', 'active', 'paused', 'completed'])->default('draft');
            
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('px_campaigns');
    }
};
