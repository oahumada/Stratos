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
        Schema::create('workforce_planning_scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->string('name'); // "Escenario Base", "Conservador", "Agresivo"
            $table->text('description')->nullable();
            $table->integer('horizon_months'); // 12, 24, 36
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'archived'])->default('draft');
            $table->year('fiscal_year');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index('fiscal_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workforce_planning_scenarios');
    }
};
