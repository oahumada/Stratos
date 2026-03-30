<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('lms_articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organization_id')->index();
            $table->unsignedBigInteger('author_id')->nullable()->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('topic')->nullable()->index();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->enum('status', ['draft', 'pending_review', 'published', 'archived'])->default('draft');
            $table->timestamp('scheduled_publish_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('people')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_articles');
    }
};
