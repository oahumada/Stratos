<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('generation_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_generation_id')->constrained('scenario_generations')->onDelete('cascade');
            $table->unsignedInteger('sequence')->default(0);
            $table->text('chunk');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('generation_chunks');
    }
};
