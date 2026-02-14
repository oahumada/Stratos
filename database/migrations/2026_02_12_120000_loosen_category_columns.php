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
        Schema::table('capabilities', function (Blueprint $table) {
            $table->string('category')->default('technical')->change();
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->string('category')->default('technical')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    // Reverting to enums is tricky because existing data might not match.
    // We leave them as strings but restore the previous defaults if needed.
    }
};
