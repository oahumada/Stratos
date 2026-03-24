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
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->text('token'); // FCM or APNs token
            $table->enum('platform', ['ios', 'android']);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_used_at')->nullable()->index();
            $table->json('metadata')->nullable(); // app_version, os_version, device_model, etc
            $table->timestamps();

            // Indexes
            $table->index(['organization_id', 'is_active']);
            $table->index(['user_id', 'platform', 'is_active']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
