<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('approval_requests')) {
            Schema::table('approval_requests', function (Blueprint $table) {
                $table->index(['approvable_type', 'approvable_id'], 'approval_requests_approvable_idx');
                $table->index(['approver_id', 'status'], 'approval_requests_approver_status_idx');
                $table->index(['status', 'expires_at'], 'approval_requests_status_expires_idx');
            });
        }

        if (Schema::hasTable('development_actions')) {
            Schema::table('development_actions', function (Blueprint $table) {
                $table->index(['development_path_id', 'status'], 'development_actions_path_status_idx');
                $table->index(['status', 'started_at'], 'development_actions_status_started_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('approval_requests')) {
            Schema::table('approval_requests', function (Blueprint $table) {
                $table->dropIndex('approval_requests_approvable_idx');
                $table->dropIndex('approval_requests_approver_status_idx');
                $table->dropIndex('approval_requests_status_expires_idx');
            });
        }

        if (Schema::hasTable('development_actions')) {
            Schema::table('development_actions', function (Blueprint $table) {
                $table->dropIndex('development_actions_path_status_idx');
                $table->dropIndex('development_actions_status_started_idx');
            });
        }
    }
};

