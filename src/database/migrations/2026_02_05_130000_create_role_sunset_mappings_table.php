<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('role_sunset_mappings')) {
            Schema::create('role_sunset_mappings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('organization_id')->index();
                $table->unsignedBigInteger('scenario_id')->nullable()->index();
                $table->unsignedBigInteger('role_id')->nullable()->index();
                $table->unsignedBigInteger('mapped_role_id')->nullable()->index();
                $table->string('sunset_reason')->nullable();
                $table->json('metadata')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                // foreign keys if tables exist in schema (best-effort)
                if (Schema::hasTable('scenarios')) {
                    $table->foreign('scenario_id')->references('id')->on('scenarios')->nullOnDelete();
                }
                if (Schema::hasTable('roles')) {
                    $table->foreign('role_id')->references('id')->on('roles')->nullOnDelete();
                    $table->foreign('mapped_role_id')->references('id')->on('roles')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_sunset_mappings');
    }
};
