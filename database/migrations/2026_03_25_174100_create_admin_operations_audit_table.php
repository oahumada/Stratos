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
        Schema::create('admin_operations_audit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('user_id');
            $table->string('operation_type'); // 'backfill', 'generate', 'import', etc
            $table->string('operation_name'); // descriptive name
            $table->string('status')->default('pending'); // pending, dry_run, running, success, failed, cancelled
            $table->json('parameters')->nullable(); // operation parameters
            $table->json('dry_run_preview')->nullable(); // preview of what would happen
            $table->json('result')->nullable(); // actual result
            $table->text('error_message')->nullable(); // if failed
            $table->integer('records_processed')->default(0);
            $table->integer('records_affected')->default(0);
            $table->decimal('duration_seconds', 8, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['organization_id', 'created_at']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_operations_audit');
    }
};
