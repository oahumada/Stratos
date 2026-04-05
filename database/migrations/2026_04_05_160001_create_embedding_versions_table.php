<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('embedding_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('version_tag');
            $table->integer('snapshot_count')->default(0);
            $table->json('metadata')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'version_tag']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('embedding_versions');
    }
};
