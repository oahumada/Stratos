<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained('lms_webhooks')->cascadeOnDelete();
            $table->string('event');
            $table->json('payload');
            $table->integer('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->string('status')->default('pending'); // pending, success, failed
            $table->timestamp('attempted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_webhook_logs');
    }
};
