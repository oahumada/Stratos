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
        Schema::create('redaction_audit_trails', function (Blueprint $table) {
            $table->id();
            $table->json('redaction_types')->comment('Types of PII redacted');
            $table->integer('count')->default(0)->comment('Number of PII instances redacted');
            $table->string('original_hash')->nullable()->comment('SHA256 hash of original text');
            $table->string('context')->nullable()->comment('API endpoint or context where redaction occurred');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade');
            $table->timestamps();

            $table->index(['organization_id', 'created_at']);
            $table->index('original_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redaction_audit_trails');
    }
};
