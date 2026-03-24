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
        Schema::create('offline_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->enum('request_type', ['approval_response', 'webhook_callback', 'webhook_registration']);
            $table->string('endpoint'); // /api/mobile/approvals/{id}/approve
            $table->json('payload'); // Request body
            $table->string('deduplication_key')->nullable()->index(); // Prevent duplicates
            $table->enum('status', ['pending', 'synced', 'duplicate', 'failed', 'error'])->default('pending')->index();
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->text('last_error')->nullable();
            $table->json('response_data')->nullable(); // Response from backend
            $table->timestamp('queued_at')->index();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['organization_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['status', 'retry_count']);
            $table->index('request_type');
            $table->unique(['user_id', 'deduplication_key', 'status']); // Prevent duplicates per user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_queue');
    }
};
