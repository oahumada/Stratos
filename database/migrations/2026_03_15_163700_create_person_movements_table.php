<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            // Type of movement: hire, exit, transfer, promotion, lateral_move
            $table->string('type');
            
            // Contextual Nodes (Optional because of hires/exits)
            $table->foreignId('from_department_id')->nullable()->constrained('departments');
            $table->foreignId('to_department_id')->nullable()->constrained('departments');
            $table->foreignId('from_role_id')->nullable()->constrained('roles');
            $table->foreignId('to_role_id')->nullable()->constrained('roles');
            
            $table->date('movement_date');
            
            // Audit & Baseline Linking
            $table->foreignId('change_set_id')->nullable()->constrained('change_sets');
            $table->json('metadata')->nullable(); // Store reasons, old salary vs new, etc.
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_movements');
    }
};
