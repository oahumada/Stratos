<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_community_knowledge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained('lms_communities')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();

            $table->index('community_id');
            $table->index(['community_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_community_knowledge');
    }
};
