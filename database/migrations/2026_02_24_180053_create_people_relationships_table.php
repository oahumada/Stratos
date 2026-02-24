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
        Schema::create('people_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('related_person_id')->constrained('people')->onDelete('cascade');
            $table->enum('relationship_type', ['manager', 'peer', 'subordinate', 'mentor', 'other']);
            $table->timestamps();

            $table->unique(['person_id', 'related_person_id', 'relationship_type'], 'idx_people_rel_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people_relationships');
    }
};
