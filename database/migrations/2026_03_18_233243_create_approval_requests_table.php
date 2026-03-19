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
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->nullableMorphs('approvable');
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->json('data')->nullable(); // edited data at approval time
            $table->text('digital_signature')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'digital_signature')) {
                $table->text('digital_signature')->nullable();
                $table->timestamp('signed_at')->nullable();
                $table->string('signature_version')->nullable();
            }
        });

        Schema::table('skills', function (Blueprint $table) {
            if (!Schema::hasColumn('skills', 'digital_signature')) {
                $table->text('digital_signature')->nullable();
                $table->timestamp('signed_at')->nullable();
                $table->string('signature_version')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_requests');
        Schema::table('roles', function (Blueprint $table) {
             $table->dropColumn(['digital_signature', 'signed_at', 'signature_version']);
        });
        Schema::table('skills', function (Blueprint $table) {
             $table->dropColumn(['digital_signature', 'signed_at', 'signature_version']);
        });
    }
};
