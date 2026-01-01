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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained();
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->string('name');
            $table->string('department')->nullable();
            $table->enum('level', ['junior', 'mid', 'senior', 'lead', 'principal'])->default('mid');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'name']);
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
