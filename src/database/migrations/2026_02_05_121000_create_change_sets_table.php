<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('change_sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->index();
            $table->unsignedBigInteger('scenario_id')->nullable()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->uuid('change_group_id')->nullable()->index();
            $table->enum('status', ['draft', 'in_review', 'approved', 'applied', 'rejected'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('effective_from')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->json('diff')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'change_group_id']);
            $table->foreign('scenario_id')->references('id')->on('scenarios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_sets');
    }
};
