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
        if (! Schema::hasTable('scenarios')) {
            return;
        }

        Schema::table('scenarios', function (Blueprint $table) {
            if (! Schema::hasColumn('scenarios', 'decision_status')) {
                $table->string('decision_status')->default('draft');
            }

            if (! Schema::hasColumn('scenarios', 'execution_status')) {
                $table->string('execution_status')->default('planned');
            }

            if (! Schema::hasColumn('scenarios', 'submitted_by')) {
                $table->unsignedBigInteger('submitted_by')->nullable();
            }

            if (! Schema::hasColumn('scenarios', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable();
            }

            if (! Schema::hasColumn('scenarios', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable();
            }

            if (! Schema::hasColumn('scenarios', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }

            if (! Schema::hasColumn('scenarios', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable();
            }

            if (! Schema::hasColumn('scenarios', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }

            if (! Schema::hasColumn('scenarios', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('scenarios')) {
            return;
        }

        $columns = [
            'decision_status',
            'execution_status',
            'submitted_by',
            'submitted_at',
            'approved_by',
            'approved_at',
            'rejected_by',
            'rejected_at',
            'rejection_reason',
        ];

        $columnsToDrop = array_values(array_filter($columns, fn (string $column): bool => Schema::hasColumn('scenarios', $column)));

        if ($columnsToDrop === []) {
            return;
        }

        Schema::table('scenarios', function (Blueprint $table) use ($columnsToDrop) {
            $table->dropColumn($columnsToDrop);
        });
    }
};
