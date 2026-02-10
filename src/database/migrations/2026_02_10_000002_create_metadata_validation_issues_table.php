<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('metadata_validation_issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('generation_id')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->json('errors')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metadata_validation_issues');
    }
};
