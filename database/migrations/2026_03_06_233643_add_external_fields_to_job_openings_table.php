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
        Schema::table('job_openings', function (Blueprint $table) {
            $table->boolean('is_external')->default(false)->after('status');
            $table->string('slug')->nullable()->unique()->after('title');
            $table->text('description')->nullable()->after('slug');
            $table->text('requirements')->nullable()->after('description');
            $table->text('benefits')->nullable()->after('requirements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_openings', function (Blueprint $table) {
            $table->dropColumn(['is_external', 'slug', 'description', 'requirements', 'benefits']);
        });
    }
};
