<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('cycle_id');
            $table->unsignedBigInteger('people_id');
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->decimal('self_score', 5, 2)->nullable();
            $table->decimal('manager_score', 5, 2)->nullable();
            $table->decimal('peer_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->decimal('calibration_score', 5, 2)->nullable();
            $table->text('strengths')->nullable();
            $table->text('development_areas')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'calibrated'])->default('pending');
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('cycle_id')->references('id')->on('performance_cycles')->onDelete('cascade');
            $table->foreign('people_id')->references('id')->on('people')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
