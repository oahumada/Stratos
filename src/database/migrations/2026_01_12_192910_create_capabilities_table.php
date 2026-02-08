<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('position_x', 5, 2)->default(50.00); // Posición en el canvas (%)
            $table->decimal('position_y', 5, 2)->default(50.00);
            $table->integer('importance')->default(3); // 1-5, define el tamaño de la burbuja
            $table->enum('type', ['technical', 'behavioral', 'strategic'])->default('technical');
            $table->enum('category', ['technical', 'leadership', 'business', 'operational'])->default('technical');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('discovered_in_scenario_id')->nullable()->constrained('scenarios')->onDelete('set null'); // -- "incubando"
            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('capabilities');
    }
};
