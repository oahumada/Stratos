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
        Schema::table('competencies', function (Blueprint $table) {
            if (!Schema::hasColumn('competencies', 'digital_signature')) {
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
        Schema::table('competencies', function (Blueprint $table) {
            $table->dropColumn(['digital_signature', 'signed_at', 'signature_version']);
        });
    }
};
