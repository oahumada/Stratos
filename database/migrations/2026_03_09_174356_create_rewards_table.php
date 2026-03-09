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
        // Catálogo de Recompensas (Beneficios)
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('points_cost');
            $table->string('category')->default('benefit'); // benefit, tool, course, cash_equivalent
            $table->integer('stock')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Historial de Canjes
        Schema::create('reward_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('reward_id')->constrained('rewards')->onDelete('cascade');
            $table->integer('points_spent');
            $table->string('status')->default('pending'); // pending, approved, delivered, rejected
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_redemptions');
        Schema::dropIfExists('rewards');
    }
};
