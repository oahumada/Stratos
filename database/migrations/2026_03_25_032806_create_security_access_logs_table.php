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
        Schema::create('security_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('organization_id')->nullable()->index();
            $table->string('event', 50);            // login, logout, login_failed, mfa_challenged, mfa_failed
            $table->string('email', 255)->nullable(); // para login_failed donde no hay user_id
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('role', 50)->nullable();
            $table->boolean('mfa_used')->default(false);
            $table->json('metadata')->nullable();   // contexto extra: guard, método, etc.
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index(['organization_id', 'occurred_at']);
            $table->index(['user_id', 'occurred_at']);
            $table->index(['event', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_access_logs');
    }
};
