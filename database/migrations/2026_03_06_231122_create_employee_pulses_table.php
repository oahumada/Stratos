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
        Schema::create('employee_pulses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->constrained('people')->onDelete('cascade');
            $table->integer('e_nps')->nullable()->comment('0-10 or 1-5 score for loyalty');
            $table->integer('stress_level')->nullable()->comment('1-5 representing stress');
            $table->integer('engagement_level')->nullable()->comment('1-5 representing engagement');
            $table->text('comments')->nullable();
            
            // IA Predictive Turnover
            $table->string('ai_turnover_risk')->nullable()->comment('low, medium, high');
            $table->text('ai_turnover_reason')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_pulses');
    }
};
