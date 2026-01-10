<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained();
            $table->string('name');
            $table->string('lifecycle_status')->default('active');
            $table->foreignId('parent_skill_id')->nullable()->constrained('skills')->nullOnDelete();
            $table->enum('category', ['technical', 'soft', 'business', 'language'])->default('technical');
            $table->text('description')->nullable();
            $table->boolean('is_critical')->default(false);
            $table->unique(['organization_id', 'name']);
            $table->index('category');
            // Skills por alcance (transversal/domain/specific)
            $table->enum('scope_type', ['transversal', 'domain', 'specific'])
                ->default('domain')->after('category');
            $table->index('scope_type');
            $table->string('domain_tag', 100)->nullable()->after('scope_type')
                ->comment('Ej: Ventas, TI, Legal, Marketing');
            $table->index('domain_tag');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
