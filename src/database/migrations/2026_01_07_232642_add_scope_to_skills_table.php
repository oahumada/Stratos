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
        Schema::table('skills', function (Blueprint $table) {
            // Skills por alcance (transversal/domain/specific)
            if (!Schema::hasColumn('skills', 'scope_type')) {
                $table->enum('scope_type', ['transversal', 'domain', 'specific'])
                    ->default('domain')->after('category');
                $table->index('scope_type');
            }
            
            if (!Schema::hasColumn('skills', 'domain_tag')) {
                $table->string('domain_tag', 100)->nullable()->after('scope_type')
                    ->comment('Ej: Ventas, TI, Legal, Marketing');
                $table->index('domain_tag');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropIndex(['scope_type']);
            $table->dropIndex(['domain_tag']);
            $table->dropColumn(['scope_type', 'domain_tag']);
        });
    }
};
