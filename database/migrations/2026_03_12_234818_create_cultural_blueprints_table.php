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
        Schema::create('cultural_blueprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');

            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->json('values')->nullable();
            $table->json('principles')->nullable();

            // Stratos Sentinel Signature (SSS) Fields
            $table->string('digital_signature')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->integer('signature_version')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultural_blueprints');
    }
};
