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
        Schema::create('person_quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('quest_id')->constrained('quests')->onDelete('cascade');
            $table->string('status')->default('active'); // active, completed, abandoned
            $table->json('progress')->nullable(); // Ej: {"courses_completed": 1, "target": 2}
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['people_id', 'quest_id']); // Una persona solo puede tener el mismo quest 1 vez (o activo 1 vez)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_quests');
    }
};
