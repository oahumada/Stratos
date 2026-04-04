<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_xapi_statements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name');
            $table->string('actor_email')->nullable();
            $table->string('verb_id');
            $table->string('verb_display');
            $table->string('object_type');
            $table->string('object_id');
            $table->string('object_name')->nullable();
            $table->float('result_score_raw')->nullable();
            $table->float('result_score_min')->nullable();
            $table->float('result_score_max')->nullable();
            $table->float('result_score_scaled')->nullable();
            $table->boolean('result_success')->nullable();
            $table->boolean('result_completion')->nullable();
            $table->string('result_duration')->nullable();
            $table->json('context_data')->nullable();
            $table->dateTime('statement_timestamp');
            $table->dateTime('stored_at');
            $table->timestamps();

            $table->index(['organization_id', 'verb_id']);
            $table->index(['actor_id']);
            $table->index(['object_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_xapi_statements');
    }
};
