<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->index();
            $table->unsignedBigInteger('role_id')->nullable()->index();
            $table->uuid('version_group_id')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('effective_from')->nullable();
            $table->enum('evolution_state', ['transformed', 'obsolescent', 'new_embryo', 'stable'])->default('stable');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->nullOnDelete();
            $table->index(['organization_id', 'version_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_versions');
    }
};
