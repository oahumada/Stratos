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
        Schema::create('strategy_executions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('strategy_id');
            $table->text('action_taken')->nullable();
            $table->text('result')->nullable();
            $table->uuid('executed_by')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strategy_executions');
    }
};
