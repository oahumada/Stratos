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
        Schema::create('assessment_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_request_id')->constrained('assessment_requests')->onDelete('cascade');
            $table->string('question');
            $table->text('answer');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_feedback');
    }
};
