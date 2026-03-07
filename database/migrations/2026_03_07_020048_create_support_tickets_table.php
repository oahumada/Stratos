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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('reporter_id');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['bug', 'improvement', 'code_quality', 'ux'])->default('bug');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_analysis', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->jsonb('context')->nullable();
            $table->string('file_path')->nullable(); // Para hallazgos en archivos específicos
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('reporter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
