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
        Schema::create('talent_pass_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_pass_id')->constrained('talent_passes')->cascadeOnDelete();
            $table->string('job_title');
            $table->string('company');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('location')->nullable();
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'freelance', 'internship'])->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['talent_pass_id', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_pass_experiences');
    }
};
