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
        Schema::create('organization_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizations_id')->constrained('organizations')->onDelete('cascade');
            $table->date('snapshot_date');
            $table->float('average_gap')->default(0);
            $table->integer('total_people')->default(0);
            $table->float('learning_velocity')->nullable(); // Gaps closed per month
            $table->float('stratos_iq')->nullable(); // Overall score based on velocity and average gap
            $table->json('metadata')->nullable(); // Extra snapshot info
            $table->timestamps();

            $table->unique(['organizations_id', 'snapshot_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_snapshots');
    }
};
