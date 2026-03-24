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
        Schema::create('mobile_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Approver
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade'); // Who requested
            $table->enum('request_type', ['escalated_action', 'manual_approval', 'policy_exception']);
            $table->string('title');
            $table->text('description');
            $table->json('context')->nullable(); // Anomaly data, action details, etc
            $table->enum('severity', ['info', 'warning', 'critical'])->default('warning');
            $table->enum('status', ['pending', 'approved', 'rejected', 'escalated', 'expired'])->default('pending')->index();
            $table->timestamp('requested_at')->index();
            $table->timestamp('expires_at')->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('approver_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('approval_data')->nullable(); // Additional data from approver
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['organization_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['organization_id', 'requested_at']);
            $table->index('severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_approvals');
    }
};
