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
        Schema::create('guide_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->index();
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->string('title');
            $table->text('question');
            $table->text('answer');
            $table->json('tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_faqs');
    }
};
