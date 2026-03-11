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
        Schema::create('event_store', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_name');
            $table->string('aggregate_type');
            $table->string('aggregate_id');
            $table->unsignedBigInteger('organization_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->jsonb('payload');
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_store');
    }
};
