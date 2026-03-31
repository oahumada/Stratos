<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('transformation_tasks', 'organization_id')) {
            Schema::table('transformation_tasks', function (Blueprint $table) {
                $table->foreignId('organization_id')
                    ->after('id')
                    ->constrained('organizations')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('transformation_tasks', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });
    }
};
