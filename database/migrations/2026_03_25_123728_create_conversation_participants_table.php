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
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('conversation_id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('people_id');
            
            // Permissions
            $table->boolean('can_send')->default(true);
            $table->boolean('can_read')->default(true);
            
            // Tracking
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->integer('unread_count')->default(0);
            
            // Audit
            $table->timestamps();
            
            // Constraints & Indexes
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('people_id')->references('id')->on('people')->onDelete('cascade');
            $table->unique(['conversation_id', 'people_id']);
            $table->index(['people_id', 'organization_id']);
            $table->index(['conversation_id', 'unread_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_participants');
    }
};
