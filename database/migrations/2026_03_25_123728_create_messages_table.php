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
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('conversation_id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('people_id');

            // Content
            $table->text('body');

            // State & delivery
            $table->enum('state', ['sent', 'delivered', 'read', 'failed'])->default('sent');

            // Metadata
            $table->uuid('reply_to_message_id')->nullable();

            // Audit
            $table->timestamps();
            $table->softDeletes();

            // Constraints & Indexes
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('people_id')->references('id')->on('people')->onDelete('cascade');
            // Self-reference FK added in AddMessageSelfReferenceForeignKey migration
            $table->index(['conversation_id', 'created_at']);
            $table->index(['organization_id', 'created_at']);
            $table->index(['people_id', 'created_at']);
            $table->index(['state', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
