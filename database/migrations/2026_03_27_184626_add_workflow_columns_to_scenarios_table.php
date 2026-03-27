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
        Schema::table('scenarios', function (Blueprint $table) {
            // Workflow state columns
            $table->string('decision_status')->default('draft')->after('status');
            $table->string('execution_status')->default('planned')->after('decision_status');

            // Submission tracking
            $table->unsignedBigInteger('submitted_by')->nullable()->after('execution_status');
            $table->timestamp('submitted_at')->nullable()->after('submitted_by');

            // Approval tracking
            $table->unsignedBigInteger('approved_by')->nullable()->after('submitted_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Rejection tracking
            $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejection_reason')->nullable()->after('rejected_at');

            // Indices for performance
            $table->index('decision_status');
            $table->index('execution_status');

            // Foreign keys
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);

            // Drop indices
            $table->dropIndex(['decision_status']);
            $table->dropIndex(['execution_status']);

            // Drop columns
            $table->dropColumn([
                'decision_status',
                'execution_status',
                'submitted_by',
                'submitted_at',
                'approved_by',
                'approved_at',
                'rejected_by',
                'rejected_at',
                'rejection_reason',
            ]);
        });
    }
};
