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
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('learning'); // learning, performance, culture, special
            $table->integer('points_reward')->default(0);
            $table->foreignId('badge_id')->nullable()->constrained('badges')->nullOnDelete();
            $table->json('requirements')->nullable(); // Ej: {"course_completed": true, "skills_required": 1}
            $table->string('status')->default('active'); // active, draft, archived
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quests');
    }
};
