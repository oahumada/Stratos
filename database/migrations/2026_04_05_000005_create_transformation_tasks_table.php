<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transformation_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')->constrained('transformation_phases')->cascadeOnDelete();
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['not_started', 'in_progress', 'blocked', 'completed', 'cancelled'])->default('not_started');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->json('blockers')->nullable();
            $table->json('dependencies')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['phase_id', 'status']);
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformation_tasks');
    }
};
