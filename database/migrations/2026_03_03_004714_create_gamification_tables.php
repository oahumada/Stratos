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
        // Puntos de Gamificación
        Schema::create('people_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->constrained('people')->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->string('reason')->nullable();
            $table->json('meta')->nullable(); // Contexto del punto (ej: skill_level_up)
            $table->timestamps();
        });

        // Catálogo de Badges
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->default('mdi-seal-variant');
            $table->string('color')->default('primary');
            $table->timestamps();
        });

        // Asignación de Badges
        Schema::create('people_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('badge_id')->constrained('badges')->onDelete('cascade');
            $table->timestamp('awarded_at')->useCurrent();
            $table->timestamps();
        });

        // Smart Alerts / Notifications
        Schema::create('smart_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('level')->default('info'); // info, success, warning, danger
            $table->string('category')->default('system'); // talent, scenario, learning, infrastructure
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->json('action_link')->nullable(); // {text: "Ir", url: "/path"}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_alerts');
        Schema::dropIfExists('people_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('people_points');
    }
};
