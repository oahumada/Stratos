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
        Schema::create('talent_pass_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_pass_id')->constrained('talent_passes')->cascadeOnDelete();
            $table->string('credential_name');
            $table->string('issuer');
            $table->date('issued_date');
            $table->date('expiry_date')->nullable();
            $table->string('credential_url')->nullable();
            $table->string('credential_id')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['talent_pass_id', 'issued_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_pass_credentials');
    }
};
