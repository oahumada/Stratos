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
        Schema::create('pulse_surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('engagement'); // engagement, climate, enps
            $table->jsonb('questions'); // [{id, text, type, options}]
            $table->boolean('is_active')->default(true);
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_surveys');
    }
};
