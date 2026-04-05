<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_messages', function (Blueprint $table) {
            $table->id();
            $table->string('execution_id')->index();
            $table->string('task_id')->nullable()->index();
            $table->string('channel')->index();
            $table->string('agent_name')->nullable();
            $table->json('payload')->nullable();
            $table->json('result')->nullable();
            $table->enum('status', ['queued', 'processing', 'completed', 'failed', 'compensated'])->default('queued')->index();
            $table->unsignedBigInteger('organization_id')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamps();

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_messages');
    }
};
