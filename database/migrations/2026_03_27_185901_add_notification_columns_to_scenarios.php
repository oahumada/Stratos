<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add notification tracking columns to scenarios table
        Schema::table('scenarios', function (Blueprint $table) {
            $table->timestamp('notifications_sent_at')->nullable()->after('execution_status');
            $table->timestamp('last_notification_resent_at')->nullable()->after('notifications_sent_at');
        });

        // Create approval_notifications table for tracking
        Schema::create('approval_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('organization_id');
            $table->unsignedBigInteger('approval_request_id');
            $table->enum('channel', ['email', 'slack', 'in_app']);
            $table->string('recipient')->nullable();
            $table->timestamp('sent_at');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('organization_id');
            $table->index('approval_request_id');
            $table->index('sent_at');
            $table->foreign('approval_request_id')
                ->references('id')
                ->on('approval_requests')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_notifications');

        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropColumn('notifications_sent_at');
            $table->dropColumn('last_notification_resent_at');
        });
    }
};
