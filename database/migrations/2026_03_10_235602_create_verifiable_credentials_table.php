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
        Schema::create('verifiable_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->constrained('people')->onDelete('cascade')->comment('The holder of the credential');
            $table->string('type')->comment('E.g. SkillAssessment, QuestCompletion, CourseCompletion');
            $table->string('issuer_name')->default('Stratos Platform')->comment('Name of the issuer');
            $table->string('issuer_did')->nullable()->comment('Decentralized Identifier for the issuer (future-proofing)');
            $table->jsonb('credential_data')->comment('The actual payload of the achievement/credential');
            $table->text('cryptographic_signature')->nullable()->comment('Placeholder for Web3 signature');
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active', 'revoked', 'expired'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifiable_credentials');
    }
};
